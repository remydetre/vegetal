<?php
class AdminOrdersController extends AdminOrdersControllerCore
{
    /*
    * module: mpm_orderdelete
    * date: 2019-07-23 12:23:30
    * version: 1.0.0
    */
    public function __construct()
    {
        $this->addRowAction('delete');
        parent::__construct();
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            $this->bulk_actions = array(
                'updateOrderStatus' => array(
                    'text' => $this->trans('Change Order Status', array(), 'Admin.Orderscustomers.Feature'),
                    'icon' => 'icon-refresh'
                ),
                'delete' => array(
                    'text' => $this->trans('Delete selected', array(), 'Admin.Actions'),
                    'icon' => 'icon-trash',
                    'confirm' => $this->trans('Delete selected items?', array(), 'Admin.Notifications.Warning')
                )
            );
        } else {
            $this->bulk_actions = array(
                'updateOrderStatus' => array('text' => $this->l('Change Order Status'), 'icon' => 'icon-refresh'),
                'delete' => array(
                    'text' => $this->l('Delete selected'),
                    'icon' => 'icon-trash',
                    'confirm' => $this->l('Delete selected items?')
                )
            );
        }
    }
}
