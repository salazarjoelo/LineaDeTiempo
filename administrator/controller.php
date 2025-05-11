<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

class LineadetiempoController extends BaseController
{
    protected $default_view = 'eventos';

    public function display($cachable = false, $urlparams = false)
    {
        LineadetiempoHelper::addSubmenu($this->input->getCmd('view', 'eventos'));
        return parent::display($cachable, $urlparams);
    }
}