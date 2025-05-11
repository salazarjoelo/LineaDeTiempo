<?php
/**
 * @package     LineaDeTiempo
 * @subpackage  com_lineadetiempo
 * @author      Joel Salazar <salazarjoelo@gmail.com>
 * @license     GNU General Public License v3+
 */

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class LineadetiempoControllerTimeline extends FormController
{
    /**
     * The default view.
     * @var string
     */
    protected $default_view = 'timeline';

    /**
     * Method to add a new record.
     * @return  mixed  True if the record can be added, a error object if not.
     */
    public function add()
    {
        if (!$this->checkEditPermission()) {
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_lineadetiempo&view=timeline', false));
            return false;
        }

        parent::add();
    }

    /**
     * Method to edit an existing record.
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key.
     * @return  boolean  True if access level check and checkout passes, false otherwise.
     */
    public function edit($key = null, $urlVar = null)
    {
        if (!$this->checkEditPermission()) {
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_lineadetiempo&view=timeline', false));
            return false;
        }

        return parent::edit($key, $urlVar);
    }

    /**
     * Method to save a record.
     * @param   string  $key     The name of the primary key of the URL variable.
     * @param   string  $urlVar  The name of the URL variable if different from the primary key.
     * @return  boolean  True if successful, false otherwise.
     */
    public function save($key = null, $urlVar = null)
    {
        Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

        if (!$this->checkEditPermission()) {
            $this->setMessage(Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_lineadetiempo&view=timeline', false));
            return false;
        }

        $data = $this->input->post->get('jform', array(), 'array');
        $model = $this->getModel('Timeline');
        $context = "$this->option.edit.$this->context";

        // Asignar el usuario actual como creador si es nuevo registro
        if (empty($data['id'])) {
            $data['created_by'] = Factory::getUser()->id;
        }

        // Validar fecha si existe
        if (isset($data['fecha']) && !$this->validateDate($data['fecha'])) {
            $this->setMessage(Text::_('COM_LINEADETIEMPO_INVALID_DATE'), 'error');
            $this->setRedirect(Route::_('index.php?option=com_lineadetiempo&view=timeline&layout=edit', false));
            return false;
        }

        if ($model->save($data)) {
            $this->setMessage(Text::_('COM_LINEADETIEMPO_EVENT_SAVED_SUCCESS'));
        } else {
            $this->setMessage($model->getError(), 'error');
        }

        $this->setRedirect(Route::_('index.php?option=com_lineadetiempo&view=timeline', false));
    }

    /**
     * Method to save the reordered items via AJAX (drag-and-drop).
     * @return  void
     */
    public function saveOrderAjax()
    {
        Session::checkToken('post') or jexit(json_encode(['success' => false, 'message' => Text::_('JINVALID_TOKEN')]));

        if (!$this->checkEditPermission()) {
            echo json_encode([
                'success' => false,
                'message' => Text::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED')
            ]);
            Factory::getApplication()->close();
        }

        $input = Factory::getApplication()->input;
        $order = $input->post->get('orden', array(), 'array');
        $model = $this->getModel('Timeline');

        try {
            // Limpiar el orden actual
            $db = Factory::getDbo();
            $query = $db->getQuery(true)
                  ->update($db->quoteName('#__lineadetiempo_eventos'))
                  ->set($db->quoteName('ordering') . ' = 0');
            $db->setQuery($query)->execute();

            // Establecer nuevo orden
            foreach ($order as $position => $id) {
                $query = $db->getQuery(true)
                      ->update($db->quoteName('#__lineadetiempo_eventos'))
                      ->set($db->quoteName('ordering') . ' = ' . (int) $position)
                      ->where($db->quoteName('id') . ' = ' . (int) $id);
                $db->setQuery($query)->execute();
            }

            echo json_encode([
                'success' => true,
                'message' => Text::_('COM_LINEADETIEMPO_ORDER_SAVED_SUCCESS')
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

        Factory::getApplication()->close();
    }

    /**
     * Method to load social media content via AJAX.
     * @return  void
     */
    public function loadSocialContent()
    {
        $input = Factory::getApplication()->input;
        $eventId = $input->getInt('eventId');
        $socialType = $input->getCmd('socialType');

        // Validar tipos de redes sociales permitidas
        $allowedSocialTypes = ['twitter', 'facebook', 'instagram', 'youtube'];
        if (!in_array($socialType, $allowedSocialTypes)) {
            echo json_encode(['error' => Text::_('COM_LINEADETIEMPO_INVALID_SOCIAL_TYPE')]);
            Factory::getApplication()->close();
        }

        $model = $this->getModel('Timeline');
        $event = $model->getItem($eventId);

        if (!$event) {
            echo json_encode(['error' => Text::_('COM_LINEADETIEMPO_EVENT_NOT_FOUND')]);
            Factory::getApplication()->close();
        }

        // Generar HTML según la red social
        $html = '';
        switch ($socialType) {
            case 'twitter':
                $html = '<blockquote class="twitter-tweet"><a href="' . $event->url_redsocial . '"></a></blockquote>';
                break;
            case 'facebook':
                $html = '<div class="fb-post" data-href="' . $event->url_redsocial . '" data-width="500"></div>';
                break;
            case 'instagram':
                $html = '<blockquote class="instagram-media" data-instgrm-permalink="' . $event->url_redsocial . '" data-instgrm-version="13"></blockquote>';
                break;
            case 'youtube':
                $videoId = $this->extractYoutubeId($event->url_redsocial);
                $html = $videoId ? '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allowfullscreen></iframe>' : '';
                break;
        }

        echo json_encode(['html' => $html]);
        Factory::getApplication()->close();
    }

    /**
     * Check edit permission for current user
     * @return  boolean
     */
    private function checkEditPermission()
    {
        $user = Factory::getUser();
        return $user->authorise('core.edit', 'com_lineadetiempo') || 
               $user->authorise('core.edit.own', 'com_lineadetiempo');
    }

    /**
     * Validate date format (YYYY-MM-DD)
     * @param   string  $date
     * @return  boolean
     */
    private function validateDate($date)
    {
        if (empty($date)) return true;
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Extract YouTube video ID from URL
     * @param   string  $url
     * @return  string|false
     */
    private function extractYoutubeId($url)
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? false;
    }
}