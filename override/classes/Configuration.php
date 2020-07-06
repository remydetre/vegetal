<?php
  class Configuration extends ConfigurationCore
  {
    /*
    * module: exportproducts
    * date: 2019-07-23 12:34:24
    * version: 4.0.8
    */
    public static function getGlobalValue($key, $id_lang = null)
    {
      self::loadConfiguration();
      return parent::getGlobalValue($key, $id_lang = null);
    }
  }