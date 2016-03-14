<?php

class Modules_JoomlaToolkit_View_Form_Installation extends pm_Form_Simple
{
    /**
     * @var Modules_JoomlaToolkit_Model_Row_Installation
     */
    private $_installation;

    public function __construct($options)
    {
        $this->_installation = $options['installation'];
        parent::__construct();
    }

    public function init()
    {
        $this->addElement('text', 'sitename', [
            'label' => $this->lmsg('components.form.installation.sitename'),
            'value' => $this->_installation->sitename,
            'readonly' => true,
        ]);
        $this->addElement('text', 'path', [
            'label' => $this->lmsg('components.form.installation.path'),
            'value' => $this->_installation->path,
            'readonly' => true,
        ]);
    }
}
