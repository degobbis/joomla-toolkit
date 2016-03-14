<?php

class Modules_JoomlaToolkit_JoomlaCli_Extensions extends Modules_JoomlaToolkit_JoomlaCli_AbstractInstallationCommand
{
    public function call()
    {
        $this->_call(['--extensions']);
    }
}