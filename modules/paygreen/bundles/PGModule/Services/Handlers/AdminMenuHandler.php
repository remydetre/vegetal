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

class PGModuleServicesHandlersAdminMenuHandler extends PGFrameworkFoundationsAbstractObject
{
    /** @var PGModuleBridgesPrestashopBridge */
    private $module;

    /** @var PGFrameworkServicesLogger */
    private $logger;

    /** @var PGModuleInterfacesPrestashopHandlerInterface */
    private $prestashopHandler;

    private $pages;

    public function __construct(PGModuleBridgesPrestashopBridge $localModule, PGFrameworkServicesLogger $logger, array $pages)
    {
        $this->module = $localModule;
        $this->logger = $logger;
        $this->pages = $pages;
    }

    /**
     * @param PGModuleInterfacesPrestashopHandlerInterface $prestashopHandler
     */
    public function setPrestashopHandler(PGModuleInterfacesPrestashopHandlerInterface $prestashopHandler)
    {
        $this->prestashopHandler = $prestashopHandler;
    }

    public function insertBackoffice()
    {
        foreach (array_keys($this->pages) as $page) {
            $this->insertPage($page);
        }
    }

    public function isValidBackoffice()
    {
        foreach (array_keys($this->pages) as $page) {
            if (!$this->hasPage($page) || !$this->isValidPage($page)) {
                return false;
            }
        }

        return true;
    }

    public function removeBackoffice()
    {
        foreach (array_keys($this->pages) as $page) {
            $this->removePage($page);
        }
    }

    /**
     * @return TabCore|null
     */
    protected function getPage($name)
    {
        $id_tab = (int) Tab::getIdFromClassName($name);

        /** @var TabCore|null $tab */
        $tab = null;

        if ($id_tab > 0) {
            $this->logger->debug("Tab '$name' successfully retrieved.");
            $tab = new Tab($id_tab);
        }

        return $tab;
    }

    /**
     * @param string $name
     * @throws Exception
     */
    protected function insertPage($name)
    {
        $config = $this->pages[$name];

        /** @var TabCore $tab */
        $tab = new Tab();

        $tab->class_name = $name;
        $tab->name[$this->module->getContext()->language->id] = $config['name'];
        $tab->module = PAYGREEN_MODULE_NAME;

        if (isset($config['parent']) && ($config['parent'] !== null)) {
            $parentName = $config['parent'];

            if ($parentName === 'root') {
                $parentName = $this->prestashopHandler->getParentMenuName();
            }

            /** @var TabCore|null $parentTab */
            $parentTab = $this->getPage($parentName);

            if ($parentTab === null) {
                throw new Exception("Parent tab '$parentName' not found.");
            }

            $tab->id_parent = $parentTab->id;
        }

        if (isset($config['icon'])) {
            $tab->icon = $config['icon'];
        }

        $tab->save();

        if (empty($tab->id)) {
            throw new Exception("Unable to create backoffice tab : '$name'.");
        }
    }

    protected function removePage($name)
    {
        /** @var TabCore|null $tab */
        $tab = $this->getPage($name);

        if ($tab !== null) {
            $tab->delete();
        }
    }

    protected function hasPage($name)
    {
        return ($this->getPage($name) !== null);
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function isValidPage($name)
    {
        $config = $this->pages[$name];

        $adminTab = $this->getPage($name);

        if ($adminTab === null) {
            throw new Exception("Admin tab not found.");
        }

        if (isset($config['parent']) && ($config['parent'] !== null)) {
            if (!$adminTab->id_parent) {
                return false;
            }
        }

        return true;
    }
}
