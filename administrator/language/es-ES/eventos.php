<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;

class LineadetiempoControllerEventos extends AdminController
{
    public function getModel($name = 'Evento', $prefix = 'LineadetiempoModel', $config = array('ignore_request' => true))
    {
        return parent::getModel($name, $prefix, $config);
    }
    
    public function saveOrderAjax()
    {
        Session::checkToken() or jexit(json_encode(['success' => false, 'message' => Text::_('JINVALID_TOKEN')]));
        
        $input = Factory::getApplication()->input;
        $pks = $input->post->get('cid', array(), 'array');
        $order = $input->post->get('order', array(), 'array');
        
        $model = $this->getModel();
        
        if ($model->saveorder($pks, $order)) {
            echo json_encode(['success' => true, 'message' => Text::_('COM_LINEADETIEMPO_ORDER_SAVED')]);
        } else {
            echo json_encode(['success' => false, 'message' => $model->getError()]);
        }
        
        Factory::getApplication()->close();
    }
}