<?php
defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Factory;

class LineadetiempoControllerImportGoogle extends AdminController
{
    public function getModel($name = 'ImportGoogle', $prefix = 'LineadetiempoModel', $config = array())
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function search()
    {
        $app = Factory::getApplication();
        $query = $app->input->getString('query', '');
        $apiKey = $this->getComponentParams()->get('google_cse_key', '');
        $cx = $this->getComponentParams()->get('google_cse_cx', '');
        
        $client = new \GuzzleHttp\Client();
        $response = $client->get("https://www.googleapis.com/customsearch/v1?q=" . urlencode($query) . "&key=$apiKey&cx=$cx");
        $results = json_decode($response->getBody(), true);
        
        $app->enqueueMessage(Text::plural('COM_LINEADETIEMPO_GOOGLE_RESULTS_FOUND', count($results['items'])));
        $app->setUserState('com_lineadetiempo.google_results', $results['items']);
        
        $this->setRedirect(Route::_('index.php?option=com_lineadetiempo&view=importgoogle', false));
    }

    private function getComponentParams()
    {
        return ComponentHelper::getParams('com_lineadetiempo');
    }
}