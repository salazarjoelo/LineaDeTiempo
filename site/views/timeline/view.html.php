<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class LineaDeTiempoViewTimeline extends BaseHtmlView
{
    protected $items;

    public function display($tpl = null)
    {
        $this->items = $this->getModel()->getItems();
        parent::display($tpl);
    }
}
