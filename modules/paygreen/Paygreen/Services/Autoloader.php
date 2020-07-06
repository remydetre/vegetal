<?php
/**
 * 2014 - 2015 Watt Is It
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PayGreen <contact@paygreen.fr>
 * @copyright 2014-2014 Watt It Is
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop <SA></SA>
 *
 */

class PaygreenServicesAutoloader
{
    /** @var string */
    private $basePath;

    private $classNames = array();

    public function __construct($basePath)
    {
        $this->basePath = $basePath;

        if (file_exists($this->getCacheFilename())) {
            $classNames = include $this->getCacheFilename();

            if (is_array($classNames)) {
                $this->classNames = $classNames;
            }
        }
    }

    /**
     * @param $className
     * @return bool
     * @throws Exception
     */
    public function autoload($className)
    {
        if (preg_match('/^Paygreen/', $className) === 0) {
            return false;
        }

        $formatedClassName = $this->snakify($className);

        $src = $this->getFilename($formatedClassName);

        $this->extendCache($className, $src);

        return $this->loadFile($src);
    }

    /**
     * @param $src string
     * @return bool
     * @throws Exception
     */
    protected function loadFile($src)
    {
        if (!is_file($src)) {
            throw new Exception("File not found : '$src'.");
        }

        require_once($src);

        return true;
    }

    protected function snakify($className)
    {
        return preg_replace("/([a-z0-9])([A-Z])/", '$1_$2', $className);
    }

    protected function getFilename($className)
    {
        $tokens = explode('_', $className);

        array_shift($tokens);

        $tokens = $this->pathFinderTokenParse($tokens);

        array_unshift($tokens, $this->basePath);

        return implode(DIRECTORY_SEPARATOR, $tokens) . '.php';
    }

    protected function pathFinderTokenParse($tokens)
    {
        $folder = $this->basePath;
        foreach ($tokens as $index => $token) {
            if (is_dir($folder . DIRECTORY_SEPARATOR . $token)) {
                $folder .= DIRECTORY_SEPARATOR . $token;
            } else {
                $filename = implode('', array_slice($tokens, $index));
                $tokens = array_merge(array_slice($tokens, 0, $index), array($filename));
                break;
            }
        }

        return $tokens;
    }

    protected function getCacheFilename()
    {
        return PAYGREEN_MODULE_VAR_DIR . DIRECTORY_SEPARATOR . 'autoload.cache.php';
    }

    protected function extendCache($className, $src)
    {
        $this->classNames[$className] = $src;

        $cache = '<?php return ' . var_export($this->classNames, true) . ';' . PHP_EOL;

        @file_put_contents($this->getCacheFilename(), $cache, LOCK_EX);
    }
}
