<?php

abstract class Modules_JoomlaToolkit_Installer
{
    public static function initDb()
    {
        $dbExists = is_file(Modules_JoomlaToolkit_Db::getDbName());
        if (!$dbExists) {
            static::createDb();
        }
    }

    private static function createDb()
    {
        $dbName = Modules_JoomlaToolkit_Db::getDbName();
        touch($dbName);
        chmod($dbName, 0600);

        $dbAdapter = Modules_JoomlaToolkit_Db::getDbAdapter();

        $initSchema = file_get_contents(__DIR__ . '/Db/init_schema.sql');
        $queries = array_map('trim', explode(';', str_replace(["\n", "\r"], '', $initSchema)));
        foreach ($queries as $query) {
            if (empty($query)) {
                continue;
            }
            $dbAdapter->query($query);
        }
    }
}
