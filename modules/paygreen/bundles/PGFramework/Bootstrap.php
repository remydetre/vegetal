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
 * Class PGFrameworkBootstrap
 * @package PGFramework
 */
class PGFrameworkBootstrap
{
    const VAR_FOLDER_CHMOD = 0755;

    /** @var PGFrameworkServicesAutoloader */
    private $autoloader;

    /** @var PGFrameworkServicesPathfinder */
    private $pathfinder;

    /** @var PGFrameworkContainer */
    private $container;

    private $path;

    /** @var PGFrameworkComponentsAppliance */
    private $appliance;

    private static $required_paths = array(
        'module',
        'var',
        'config',
        'media',
        'static'
    );

    private static $required_constants = array(
        'PAYGREEN_VENDOR_DIR',
        'PAYGREEN_VAR_DIR',
        'PAYGREEN_CONFIG_DIR',
        'PAYGREEN_MEDIA_DIR',
        'PAYGREEN_MODULE_VERSION'
    );

    private static $default_libraries = array(
        'PGFramework:/Functions/development.php',
        'PGFramework:/Functions/templating.php'
    );

    private static $default_created_folders = array(
        'var',
        'config'
    );

    private static $default_vendors = array(
        'PGFramework' => 'PGFramework',
        'PGClient' => 'PGClient',
        'PGServer' => 'PGServer',
        'PGView' => 'PGView',
        'PGForm' => 'PGForm',
        'PGDomain' => 'PGDomain',
        'APPbackoffice' => 'APPbackoffice',
        'APPfrontoffice' => 'APPfrontoffice',
        'APPiss' => 'APPiss',
        'APPdevelopment' => 'APPdevelopment',
        'PGModule' => 'PGModule'
    );

    /**
     * PGFrameworkBootstrap constructor.
     * @param string $path The path to all bundle folders.
     * @throws Exception
     */
    public function __construct($path)
    {
        $this->path = $path;

        define('PAYGREEN_ENV', getenv('PAYGREEN_ENV') ? Tools::strtoupper(getenv('PAYGREEN_ENV')) : 'PROD');

        foreach (self::$required_constants as $constant) {
            if (!defined($constant)) {
                throw new Exception("'$constant' is not defined.");
            }
        }
    }

    /**
     * @return PGFrameworkContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return PGFrameworkServicesAutoloader
     */
    public function getAutoloader()
    {
        return $this->autoloader;
    }

    /**
     * @return PGFrameworkServicesPathfinder
     */
    public function getPathfinder()
    {
        return $this->pathfinder;
    }

    /**
     * @param string $name
     * @return self
     * @throws Exception
     */
    public function buildAppliance($name)
    {
        $DS = DIRECTORY_SEPARATOR;

        require_once $this->path . $DS . 'PGFramework' . $DS . 'Components' . $DS . 'Appliance.php';

        $this->appliance = new PGFrameworkComponentsAppliance($name, PAYGREEN_MODULE_VERSION);

        $this->addVendors(self::$default_vendors);

        return $this;
    }

    public function addVendors(array $vendors)
    {
        if ($this->appliance === null) {
            throw new Exception("Appliance must be builded before adding vendors.");
        }

        foreach ($vendors as $vendor) {
            $this->appliance->addVendor($vendor);
        }

        return $this;
    }

    /**
     * @param array $paths
     * @return self
     * @throws Exception
     */
    public function buildPathfinder(array $paths)
    {
        if ($this->appliance === null) {
            throw new Exception("Appliance must be builded before building pathfinder.");
        }

        $DS = DIRECTORY_SEPARATOR;

        $this->verifyPaths($paths);

        require_once $this->path . $DS . 'PGFramework' . $DS . 'Services' . $DS . 'Pathfinder.php';

        $paths = $this->getPathfinderConfiguration($paths);

        $this->pathfinder = new PGFrameworkServicesPathfinder($this->appliance, $paths);

        return $this;
    }

    /**
     * @param array $paths
     * @throws Exception
     */
    protected function verifyPaths(array $paths)
    {
        foreach (self::$required_paths as $pathName) {
            if (!isset($paths[$pathName])) {
                throw new Exception("Path not found : '$pathName'.");
            }
        }
    }

    protected function getPathfinderConfiguration(array $paths)
    {
        $pathfinderConfiguration = array();

        foreach ($this->appliance->getVendors() as $vendor) {
            $pathfinderConfiguration[$vendor] = "{$this->path}/$vendor";
        }

        return array_merge($pathfinderConfiguration, $paths);
    }

    /**
     * @param array $additionalFolders
     * @return self
     * @throws Exception
     */
    public function createVarFolder(array $additionalFolders = array())
    {
        if ($this->pathfinder === null) {
            throw new Exception("PathFinder must be initialized before loading functions.");
        }

        $folders = array_merge(
            self::$default_created_folders,
            $additionalFolders
        );

        foreach ($folders as $folder) {
            if (strstr($folder, ':') !== false) {
                list($base, $src) = explode(':', $folder, 2);
            } else {
                $base = $folder;
                $src = '';
            }

            $path = $this->pathfinder->toAbsolutePath($base, $src);

            if (!is_dir($path)) {
                mkdir($path, self::VAR_FOLDER_CHMOD, true);
            }
        }

        return $this;
    }

    /**
     * @param array $additionalLibraries
     * @return self
     * @throws Exception
     */
    public function preloadFunctions(array $additionalLibraries = array())
    {
        if ($this->pathfinder === null) {
            throw new Exception("PathFinder must be initialized before loading functions.");
        }

        $libraries = array_merge(
            self::$default_libraries,
            $additionalLibraries
        );

        foreach ($libraries as $library) {
            list($base, $src) = explode(':', $library, 2);

            require_once $this->pathfinder->toAbsolutePath($base, $src);
        }

        return $this;
    }

    /**
     * @param array $additionalVendors
     * @return self
     * @throws Exception
     */
    public function registerAutoloader()
    {
        if ($this->pathfinder === null) {
            throw new Exception("PathFinder must be initialized before loading functions.");
        }

        require_once $this->pathfinder->toAbsolutePath('PGFramework', '/Interfaces/StorageInterface.php');
        require_once $this->pathfinder->toAbsolutePath('PGFramework', '/Foundations/AbstractStorage.php');
        require_once $this->pathfinder->toAbsolutePath('PGFramework', '/Foundations/AbstractStorageFile.php');
        require_once $this->pathfinder->toAbsolutePath('PGFramework', '/Components/Storages/JSONFileStorage.php');

        $filename = PAYGREEN_VAR_DIR . DIRECTORY_SEPARATOR . 'autoload.cache.json';

        $storage = new PGFrameworkComponentsStoragesJSONFileStorage($filename);

        require_once $this->pathfinder->toAbsolutePath('PGFramework', '/Services/Autoloader.php');

        $this->autoloader = new PGFrameworkServicesAutoloader($storage);

        foreach ($this->appliance->getVendors() as $vendor) {
            $path = $this->pathfinder->toAbsolutePath($vendor);
            $this->autoloader->addVendor($vendor, $path);
        }

        spl_autoload_register(array($this->autoloader, 'autoload'), true, true);

        return $this;
    }

    /**
     * @param array $additionalServicePaths
     * @param array $additionalParametersPaths
     * @return self
     * @throws Exception
     */
    public function buildContainer(array $additionalServicePaths = array(), array $additionalParametersPaths = array())
    {
        if ($this->pathfinder === null) {
            throw new Exception("PathFinder must be initialized before loading functions.");
        } elseif ($this->autoloader === null) {
            throw new Exception("Autoloader must be initialized before loading functions.");
        }

        $this->container = PGFrameworkContainer::getInstance();

        $this->loadServiceLibrary($additionalServicePaths);
        $this->loadParameters($additionalParametersPaths);

        return $this;
    }

    /**
     * @param array $additionalPaths
     * @throws Exception
     */
    private function loadServiceLibrary(array $additionalPaths = array())
    {
        $paths = $this->pathfinder->reviewVendorPaths('/_config/services');

        foreach ($additionalPaths as $additionalPath) {
            $paths[] = $this->pathfinder->toAbsolutePath($additionalPath);
        }

        /** @var PGFrameworkComponentsServiceLibrary $library */
        $library = $this->container->get('service.library');

        foreach ($paths as $path) {
            $library->addConfigurationFolder($path);
        }
    }

    /**
     * @param array $additionalPaths
     * @throws Exception
     */
    private function loadParameters(array $additionalPaths = array())
    {
        $paths = $this->pathfinder->reviewVendorPaths('/_config/parameters');

        foreach ($additionalPaths as $additionalPath) {
            $paths[] = $this->pathfinder->toAbsolutePath($additionalPath);
        }

        /** @var PGFrameworkComponentsParameters $parameters */
        $parameters = $this->container->get('parameters');

        foreach ($paths as $path) {
            $parameters->addParametersFolder($path);
        }
    }

    private function buildVendorPaths($src)
    {
        $paths = array();

        $vendors = $this->autoloader->getVendors();

        foreach (array_keys($vendors) as $vendor) {
            $paths[] = "$vendor:$src";
        }

        return $paths;
    }

    /**
     * @param array $additionalServices
     * @return self
     * @throws Exception
     */
    public function insertStaticServices(array $additionalServices = array())
    {
        if ($this->container === null) {
            throw new Exception("Container must be initialized before inserting services.");
        }

        $defaultServices = array(
            'appliance' => $this->appliance,
            'autoloader' => $this->autoloader,
            'pathfinder' => $this->pathfinder,
            'bootstrap' => $this
        );

        $services = array_merge(
            $defaultServices,
            $additionalServices
        );

        foreach ($services as $name => $service) {
            $this->container->set($name, $service);
        }

        return $this;
    }

    /**
     * @param int $flags
     * @return self
     * @throws Exception
     */
    public function setup($flags = PGFrameworkServicesHandlersSetupHandler::ALL)
    {
        if ($this->container === null) {
            throw new Exception("Container must be initialized before running setup.");
        }

        /** @var PGFrameworkServicesHandlersSetupHandler $setupHandler */
        $setupHandler = $this->container->get('handler.setup');

        $setupHandler->run($flags);

        return $this;
    }

    /**
     * @return self
     * @throws Exception
     */
    public function activateDetailedLogs()
    {
        $this->container->get('logger')->warning("Method 'activateDetailedLogs' is deprecated. Just remove call to it in bootstrap file.");

        return $this;
    }
}
