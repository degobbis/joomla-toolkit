<?php
pm_Loader::registerAutoload();
pm_Context::init('joomla-toolkit');

try {
    Modules_JoomlaToolkit_Installer::initDb();
} catch (pm_Exception $e) {
    echo $e->getMessage() . "\n";
    exit(1);
}
exit(0);
