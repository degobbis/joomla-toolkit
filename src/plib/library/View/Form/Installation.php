<?php

class Modules_JoomlaToolkit_View_Form_Installation extends pm_Form_Simple
{
    /**
     * @var Modules_JoomlaToolkit_Model_Row_Installation
     */
    private $_installation;
    private $_returnUrl;

    public function __construct($options)
    {
        $this->_installation = $options['installation'];
        $this->_returnUrl = $options['returnUrl'];
        parent::__construct();
    }

    public function init()
    {
        $this->addElement('text', 'path', [
            'label' => $this->lmsg('components.form.installation.path'),
            'value' => $this->_installation->path,
            'required' => true,
        ]);

        $this->addControlButtons([
            'cancelLink' => $this->_returnUrl,
        ]);
    }

    public function process()
    {
        $this->_installation->path = $this->path->getValue();
        $this->_installation->save();
    }
}
