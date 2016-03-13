<?php

class Modules_JoomlaToolkit_JoomlaCli_Info extends Modules_JoomlaToolkit_JoomlaCli_AbstractInstallationCommand
{
    public function call()
    {
        return json_decode($this->_call(['--info']), true);
    }
}