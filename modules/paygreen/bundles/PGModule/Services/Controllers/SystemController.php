<?php
/**
 * 2014 - 2019 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons BY-ND 4.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://creativecommons.org/licenses/by-nd/4.0/fr/
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to contact@paygreen.fr so we can send you a copy immediately.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014 - 2019 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   2.7.6
 */

class PGModuleServicesControllersSystemController extends PGFrameworkFoundationsAbstractController
{
    public function deleteLogFileAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGFrameworkServicesPathfinder $pathfinder */
        $pathfinder = $this->getService('pathfinder');

        $files = $this->getFileList();

        $file = $request->get('filename');
        $filename = $pathfinder->toAbsolutePath('var', "/$file");

        /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
        $response = $this->buildChainedResponse();

        if (in_array($file, $files) && file_exists($filename)) {
            unlink($filename);
            $response->add($response::SUCCESS, 'system.logs.actions.delete.success');
        } elseif(!in_array($file, $files)) {
            $response->add($response::FAILURE, 'system.logs.errors.invalid_file');
        } elseif(!file_exists($filename)) {
            $response->add($response::FAILURE, 'system.logs.errors.file_not_found');
        }

        return $response;
    }

    public function downloadLogFileAction(PGFrameworkComponentsIncomingRequest $request)
    {
        /** @var PGFrameworkServicesPathfinder $pathfinder */
        $pathfinder = $this->getService('pathfinder');

        $files = $this->getFileList();

        $file = $request->get('filename');
        $filename = $pathfinder->toAbsolutePath('var', "/$file");

        /** @var PGFrameworkComponentsResponsesChainQualifiedMessagesResponse $response */
        $response = $this->buildChainedResponse();

        if (in_array($file, $files) && file_exists($filename)) {
            header('Content-Description: File Transfer');
            header('Content-Type: text');
            header('Content-Disposition: attachment; filename="' . $file . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));

            readfile($filename);

            exit;
        } elseif(!in_array($file, $files)) {
            $response->add($response::FAILURE, 'system.logs.errors.invalid_file');
        } elseif(!file_exists($filename)) {
            $response->add($response::FAILURE, 'system.logs.errors.file_not_found');
        }

        return $response;
    }

    public function displayDataAction()
    {
        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGModuleServicesModuleFacade $moduleFacade */
        $moduleFacade = $this->getService('facade.module');

        /** @var PGDomainServicesPaygreenFacade $paygreenFacade */
        $paygreenFacade = $this->getService('paygreen.facade');

        try {
            if (function_exists('curl_version')) {
                $curl_data = curl_version();
            } else {
                $curl_data = array(
                    'version' => 'NA',
                    'ssl_version' => 'NA'
                );
            }

            $response = new PGFrameworkComponentsResponsesTemplateResponse();

            $response
                ->setTemplate('views/templates/admin', 'system')
                ->addData('platforme', $moduleFacade->getApplicationName())
                ->addData('version_platforme', $moduleFacade->getApplicationVersion())
                ->addData('version_php', PHP_VERSION)
                ->addData('version_module', PAYGREEN_MODULE_VERSION)
                ->addData('version_framework', $paygreenFacade::VERSION)
                ->addData('version_curl', $curl_data['version'])
                ->addData('version_ssl', $curl_data['ssl_version'])
                ->addData('log_data', $this->getLogData())
            ;
        } catch (Exception $exception) {
            $logger->error("Error during system footer building : " . $exception->getMessage(), $exception);

            $response = new PGFrameworkComponentsResponsesChainQualifiedMessagesResponse();

            $response->add($response::FAILURE, 'account.backoffice.errors.footer');
        }

        return $response;
    }

    private function getFileList()
    {
        $files = array('module.log', 'api.log');

        if (PAYGREEN_ENV === 'DEV') {
            $files[] = 'error.log';
        }

        return $files;
    }

    private function getLogData()
    {
        $files = $this->getFileList();

        $logs = array();

        /** @var PGFrameworkServicesPathfinder $pathfinder */
        $pathfinder = $this->getService('pathfinder');

        foreach($files as $file) {
            $filename = $pathfinder->toAbsolutePath('var', "/$file");
            $datetime = new DateTime();

            if (is_file($filename)) {
                $logs[] = array(
                    'name' => $file,
                    'size' => $this->getHumanFilesize(filesize($filename)),
                    'updatedAt' => $datetime->setTimestamp(filemtime($filename))->format('d M Y H:i:s'),
                    'action' => true
                );
            } else {
                $logs[] = array(
                    'name' => $file,
                    'size' => "Log inexistant",
                    'updatedAt' => "NA",
                    'action' => false
                );
            }
        }


        return $logs;
    }

    private function getHumanFilesize($bytes, $decimals = 2)
    {
        $sz = 'BKMGTP';
        $factor = floor((Tools::strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
