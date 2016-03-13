<?php

class Modules_JoomlaToolkit_JoomlaCli_Sitename extends Modules_JoomlaToolkit_JoomlaCli_AbstractCommand
{
    /**
     * @var string
     */
    private $_path;

    public function __construct($path)
    {
        $this->_path = $path;
        parent::__construct();
    }

    protected function _getInstallationPath()
    {
        return $this->_path;
    }

    public function call()
    {
        $result = json_decode($this->_call(['--sitename']));
        return $result->sitename ? $result->sitename : "empty";
    }
}