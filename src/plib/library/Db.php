<?php

abstract class Modules_JoomlaToolkit_Db
{
    /**
     * @var Zend_Db_Adapter_Abstract|null
     */
    private static $_dbAdapter = null;

    /**
     * @return string
     */
    public static function getDbName()
    {
        return pm_Context::getVarDir() . pm_Context::getModuleId() . '.sqlite3';
    }

    /**
     * @return null|Zend_Db_Adapter_Abstract
     */
    public static function getDbAdapter()
    {
        if (!self::$_dbAdapter) {
            self::$_dbAdapter = new Zend_Db_Adapter_Pdo_Sqlite(['dbname' => static::getDbName()]);
            self::$_dbAdapter->getConnection()->exec('PRAGMA foreign_keys = ON;');
        }
        return self::$_dbAdapter;
    }

    /**
     * @param Zend_Db_Adapter_Abstract $dbAdapter
     */
    public static function setDbAdapter(Zend_Db_Adapter_Abstract $dbAdapter)
    {
        self::$_dbAdapter = $dbAdapter;
    }
}
