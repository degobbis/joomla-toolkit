<?php

abstract class Modules_JoomlaToolkit_JoomlaCli_AbstractCommand
{
    /**
     * @return string Absolute path to installation with slash in the end
     */
    abstract protected function _getInstallationPath();
    abstract public function call();

    public function __construct()
    {
        $this->_checkUpdateScript();
    }

    protected function _call(array $args = [])
    {
        return Modules_JoomlaToolkit_CallSbinWrapper::callUpdateWrapper(
            $this->_getInstallationPath() . '/cli/update.php',
            $args
        );
    }

    protected function _checkUpdateScript()
    {
        // TODO: remove when will be available in core
        $fileManager = new pm_ServerFileManager();
        $file = $this->_getInstallationPath() . '/cli/update.php';
        /*if ($fileManager->fileExists($file)) {
            return;
        }*/
        $content = file_get_contents('https://raw.githubusercontent.com/joomla-projects/cli-update/develop/src/cli/update.php');
        $fileManager->filePutContents($file, $content);
    }
}