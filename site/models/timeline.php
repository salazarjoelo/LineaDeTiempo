<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Model\ListModel;

class LineaDeTiempoModelTimeline extends ListModel
{
    public function getItems()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__lineadetiempo_items')
            ->where('state = 1')
            ->order('date ASC');
        return $db->setQuery($query)->loadObjectList();
    }
}
