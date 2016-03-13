<?php

class Modules_JoomlaToolkit_JoomlaCli_Core extends Modules_JoomlaToolkit_JoomlaCli_AbstractInstallationCommand
{
    public function call()
    {
        $this->_call(['--core']);
    }
}