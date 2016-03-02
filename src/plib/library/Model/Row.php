<?php

class Modules_JoomlaToolkit_Model_Row extends Zend_Db_Table_Row_Abstract
{
    /**
     * Check if object is a new object
     *
     * @return bool
     */
    public function isNew()
    {
        return (empty($this->_cleanData));
    }
}
