<?php
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;
use Joomla\Database\DatabaseDriver;

class LineadetiempoTableEvento extends Table
{
    public function __construct(DatabaseDriver $db)
    {
        parent::__construct('#__lineadetiempo_eventos', 'id', $db);
    }

    public function publish($pks = null, $state = 1, $userId = 0)
    {
        $k = $this->_tbl_key;
        $query = $this->_db->getQuery(true)
            ->update($this->_db->quoteName($this->_tbl))
            ->set($this->_db->quoteName('published') . ' = ' . (int) $state)
            ->where($this->_db->quoteName($k) . ' IN (' . implode(',', $pks) . ')');
        
        $this->_db->setQuery($query)->execute();
        return true;
    }
}