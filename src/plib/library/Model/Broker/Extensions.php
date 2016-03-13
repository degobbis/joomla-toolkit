<?php

class Modules_JoomlaToolkit_Model_Broker_Extensions extends Modules_JoomlaToolkit_Model_Broker
{
    protected $_name = 'extensions';
    protected $_rowClass = 'Modules_JoomlaToolkit_Model_Row_Extension';

    protected $_referenceMap = [
        'Installation' => [
            'columns'           => 'installationId',
            'refTableClass'     => 'Modules_JoomlaToolkit_Model_Broker_Installations',
            'refColumns'        => 'id'
        ],
    ];
}
