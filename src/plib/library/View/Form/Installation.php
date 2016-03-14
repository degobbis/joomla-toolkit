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
        $url = $this->_installation->getUrl();
        $fields = [
            'sitename' => $this->_installation->sitename,
            'path' => $this->_installation->path,
            'version' => $this->_installation->version,
            'url' => '<a href="' . $url . '" target="_blank">' . $url . '</a>',
        ];
        foreach ($fields as $name => $value) {
            $this->addElement('text', $name, [
                'label' => $this->lmsg('components.form.installation.' . $name),
                'value' => $value,
                'readonly' => true,
            ]);
        }
    }
}
