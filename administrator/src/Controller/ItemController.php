<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route; // Para construir URLs de redirección
use Joomla\CMS\Language\Text; // Para mensajes
use Joomla\CMS\Factory;       // Para Factory::getApplication()
use Joomla\CMS\Session\Session; // Para checkToken()
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Application\CMSApplication;
use Joomla\Input\Input;

/**
 * Item edit controller class for LineaDeTiempo component (Administrator).
 *
 * @since  1.0.0
 */
class ItemController extends FormController
{
    /**
     * The prefix for the views.
     * Generalmente, 'item' para la vista singular del formulario.
     * @var    string
     * @since  1.0.0
     */
    protected $view_item = 'item';

    /**
     * The prefix for the list views.
     * Generalmente, 'items' para la vista de lista.
     * @var    string
     * @since  1.0.0
     */
    protected $view_list = 'items';

    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     * @param   ?MVCFactoryInterface $factory  The factory.
     * @param   ?CMSApplication      $app      The Application object.
     * @param   ?Input               $input    Input object.
     *
     * @since   1.0.0
     */
    public function __construct(array $config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        // El contexto para el FormController es importante para la sesión y redirecciones.
        // Se forma como 'option.viewname'. En nuestro caso, 'com_lineadetiempo.item'.
        // FormController lo maneja si $this->option y $this->view_item están bien.
        // $this->option se establece automáticamente.
        $config['text_prefix'] = 'COM_LINEADETIEMPO'; // Para mensajes de FormController
        parent::__construct($config, $factory, $app, $input);

        // FormController ya registra las tareas estándar:
        // add, edit, save, apply, save2new, save2copy, cancel, publish (si el modelo lo soporta)
    }

    // FormController (y su padre AdminController) manejan la mayoría de las tareas.
    // Solo necesitas sobrescribir métodos si tienes lógica muy específica.
    // Por ejemplo, el método allowAdd y allowEdit que tenías en tu J3 controller:
    // En J5, FormController::add() y FormController::edit() ya hacen comprobaciones de ACL
    // usando $this->allowAdd y $this->allowEdit que a su vez usan ContentHelper::getActions.
    // Así que usualmente no necesitas sobrescribirlos si tu access.xml es correcto.

    // Ejemplo de cómo podrías sobrescribir 'save' si necesitas hacer algo extra:
    /*
    public function save($key = null, $urlVar = 'id'): bool // Asegura que la firma coincida con el padre
    {
        // Validar el token CSRF
        $this->checkToken(); // o Session::checkToken('post') or Session::checkToken('get')

        $app   = Factory::getApplication();
        $model = $this->getModel('Item'); // FormController usa getModel() con el nombre de la vista por defecto (item)
        $table = $model->getTable();
        $data  = $this->input->post->get('jform', [], 'array');
        $form  = $model->getForm($data, false);

        if (!$form) {
            $app->enqueueMessage($model->getError(), 'error');
            return false;
        }

        // Validar los datos del formulario
        $validData = $model->validate($form, $data);

        if ($validData === false) {
            // Los errores se deberían haber encolado por el modelo
            // Mostrar el formulario de nuevo
            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($validData[$table->getKeyName()] ?? null, $urlVar), false
                )
            );
            return false;
        }

        // Intentar guardar los datos
        if (!$model->save($validData)) {
            // Los errores se deberían haber encolado por el modelo
            $app->enqueueMessage($model->getError(), 'error'); // Asegurar que el error se muestra
             $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&view=' . $this->view_item
                    . $this->getRedirectToItemAppend($validData[$table->getKeyName()] ?? null, $urlVar), false
                )
            );
            return false;
        }

        $this->setMessage(Text::_($this->text_prefix . ($validData[$table->getKeyName()] ? '_ITEM_SAVED_SUCCESS' : '_ITEM_SAVED_NEW_SUCCESS')));

        // Decidir a dónde redirigir según la tarea (apply, save, save2new)
        $task = $this->getTask();

        if ($task === 'save2new') {
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&task=' . $this->taskMap['add'], false));
        } elseif ($task === 'apply') {
            $this->setRedirect(
                Route::_(
                    'index.php?option=' . $this->option . '&task=' . $this->taskMap['edit'] . $this->getRedirectToItemAppend($table->{$table->getKeyName()}, $urlVar), false
                )
            );
        } else { // Por defecto (save o desconocido) ir a la lista
            $this->setRedirect(Route::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
        }
        
        // Limpiar el contexto de edición
        $app->setUserState($this->context . '.data', null);


        return true;
    }
    */
}