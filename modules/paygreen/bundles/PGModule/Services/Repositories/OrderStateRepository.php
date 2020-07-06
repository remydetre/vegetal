<?php
/**
 * 2014 - 2020 Watt Is It
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
 * @copyright 2014 - 2020 Watt Is It
 * @license   https://creativecommons.org/licenses/by-nd/4.0/fr/ Creative Commons BY-ND 4.0
 * @version   3.0.1
 */

/**
 * Class PGModuleServicesRepositoriesOrderStateRepository
 * @package PGModule\Services\Repositories
 *
 * @method PGModuleEntitiesOrderState createWrappedEntity(array $data = array())
 */
class PGModuleServicesRepositoriesOrderStateRepository extends PGModuleFoundationsAbstractPrestashopRepository implements PGDomainInterfacesRepositoriesOrderStateRepositoryInterface
{
    const ENTITY = 'OrderState';

    private $definitions = array();

    public function __construct(array $definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function create($code, $name, array $metadata = array())
    {
        /** @var PGFrameworkServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        if (!array_key_exists($code, $this->definitions)) {
            $message = "Code definition not found : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!is_array($this->definitions[$code])) {
            $message = "Uncorrectly defined order state : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!array_key_exists('create', $this->definitions[$code]) || !$this->definitions[$code]['create']) {
            $message = "This state can not be created : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!array_key_exists('source', $this->definitions[$code])) {
            $message = "Target state has no 'source' field : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!is_array($this->definitions[$code]['source'])) {
            $message = "Target state 'source' must be an array : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!array_key_exists('name', $this->definitions[$code]['source'])) {
            $message = "Target state has no 'source' field : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!is_string($this->definitions[$code]['source']['name'])) {
            $message = "Target state 'source' must be an array : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!array_key_exists('en', $metadata)) {
            $message = "Target state has no 'en' field in metadata : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!array_key_exists('color', $metadata)) {
            $message = "Target state has no 'color' field in metadata : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        } elseif (!array_key_exists('filename', $metadata)) {
            $message = "Target state has no 'filename' field in metadata : '$code'.";
            throw new PGFrameworkExceptionsConfigurationException($message);
        }

        $setting_name = $this->definitions[$code]['source']['name'];

        $orderStatePrimary = $settings->get($setting_name);

        $orderState = null;

        if ($orderStatePrimary) {
            $orderState = $this->findByPrimary($orderStatePrimary);

            if ($orderState === null) {
                $logger->warning("Order state '$name' not found. Recreating it.");
            }
        }

        if ($orderState === null) {
            $orderState = $this->createOrderStateEntity($setting_name, $name, $metadata);
        }

        return $orderState;
    }

    /**
     * @param $setting_name
     * @param $name
     * @param array $metadata
     * @return PGModuleEntitiesOrderState
     * @throws Exception
     */
    protected function createOrderStateEntity($setting_name, $name, array $metadata = array())
    {
        /** @var PGFrameworkServicesSettings $settings */
        $settings = $this->getService('settings');

        /** @var PGFrameworkServicesLogger $logger */
        $logger = $this->getService('logger');

        /** @var PGFrameworkServicesPathfinder $pathfinder */
        $pathfinder = $this->getService('pathfinder');

        $logger->notice("Creating order state : '$name'.");

        $names = array();

        foreach (Language::getLanguages() as $language) {
            $iso = Tools::strtolower($language['iso_code']);
            $id_lang = $language['id_lang'];

            if ($iso === 'fr') {
                $names[$id_lang] = $name;
            } elseif ($iso === 'en') {
                $names[$id_lang] = $metadata['en'];
            }
        }

        $entity = $this->createWrappedEntity(array(
            'name' => $names,
            'module_name' => PAYGREEN_MODULE_NAME,
            'send_email' => false,
            'color' => $metadata['color'],
            'hidden' => false,
            'delivery' => false,
            'logable' => false,
            'invoice' => $metadata['invoice'],
            'paid' => $metadata['paid']
        ));

        $this->insertLocalEntity($entity->getLocalEntity());

        $settings->set($setting_name, $entity->id());

        $sourceFile = $pathfinder->toAbsolutePath('static', '/pictures/PGModule/order-states/' . $metadata['filename']);
        $targetFile = _PS_IMG_DIR_ . 'os' . DS . $entity->id() . '.gif';

        if (file_exists($sourceFile)) {
            @Tools::copy($sourceFile, $targetFile);
        }

        return $entity;
    }

    public function wrapEntity($localEntity)
    {
        return new PGModuleEntitiesOrderState($localEntity);
    }
}
