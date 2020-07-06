<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class mpm_orderdelete extends Module
{
    public function __construct()
    {
        $this->name = 'mpm_orderdelete';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'MyPrestaModules';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Delete Orders');
        $this->description = $this->l('This module allow you to delete orders and all related data from backoffice.');
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }
        return true;
    }
}
