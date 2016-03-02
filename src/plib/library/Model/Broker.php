<?php

class Modules_JoomlaToolkit_Model_Broker extends Zend_Db_Table_Abstract
{
    protected $_rowClass = 'Modules_JoomlaToolkit_Model_Row';

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->_setAdapter(Modules_JoomlaToolkit_Db::getDbAdapter());
    }

    /**
     * Find one object
     *
     * @param int $id
     * @return null|Row
     */
    public function findOne($id)
    {
        $rowset = $this->find($id);
        return $rowset ? $rowset->current() : null;
    }

    /**
     * Find objects by field value/values
     *
     * @param string $name
     * @param string|array $value
     * @param bool $exact
     * @param int $limit
     * @return \Zend_Db_Table_Rowset_Abstract
     */
    public function findByField($name, $value, $exact = true, $limit = null)
    {
        $quotedName = $this->getAdapter()->quoteIdentifier($name);

        if (is_array($value)) {
            $where = $this->getAdapter()->quoteInto("$quotedName IN (?)", $value);
        } elseif ($exact) {
            $where = $this->getAdapter()->quoteInto("$quotedName = ?", $value);
        } else {
            $where = $this->getAdapter()->quoteInto("$quotedName LIKE ?", "%$value%");
        }

        return $this->fetchAll($where, null, $limit);
    }
}
