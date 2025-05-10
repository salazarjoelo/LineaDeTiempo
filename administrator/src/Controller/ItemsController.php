<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface; // Para el constructor
use Joomla\CMS\Session\Session; // Para el token de sesión
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel; // Para tipar getModel

/**
 * Items list controller class for Timeline component (Administrator).
 * Manages operations on lists of items (publish, unpublish, delete etc.)
 *
 * @since  1.0.0
 */
class ItemsController extends AdminController
{
    /**
     * The prefix to use with controller messages.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $text_prefix = 'COM_TIMELINE'; // Usado para mensajes como COM_TIMELINE_N_ITEMS_PUBLISHED

    /**
     * Constructor.
     *
     * @param   array                $config   An optional associative array of configuration settings.
     * @param   MVCFactoryInterface  $factory  The factory.
     * @param   ?CMSApplication      $app      The Application object.
     * @param   ?Input               $input    Input object.
     *
     * @since   1.0.0
     */
    public function __construct(array $config = [], MVCFactoryInterface $factory = null, $app = null, $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        // AdminController ya registra tareas comunes como publish, unpublish, delete, checkin.
        // No necesitas registrarlas de nuevo a menos que quieras un comportamiento personalizado.
        // Por ejemplo, si tuvieras una tarea 'archive':
        // $this->registerTask('archive', 'publish'); // Reutiliza el método publish si la lógica es similar
    }

    /**
     * Proxy for getModel.
     * Para asegurar que se cargue el modelo plural `ItemsModel` para la lista.
     *
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  The prefix for the class name.
     * @param   array   $config  Configuration array for model.
     *
     * @return  BaseDatabaseModel|false  Model object on success; otherwise false.
     *
     * @since   1.0.0
     */
    public function getModel(string $name = 'Items', string $prefix = 'Administrator', array $config = []): BaseDatabaseModel|false
    {
        // El nombre 'Items' (plural) debería hacer que cargue `ItemsModel.php`
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }
    
    // Los métodos publish(), unpublish(), delete(), etc., son heredados de AdminController.
    // Solo necesitas sobrescribirlos si necesitas una lógica muy específica ANTES o DESPUÉS
    // de que la acción principal se ejecute (en cuyo caso llamas a parent::nombreMetodo()).
    // Por ejemplo, si al borrar necesitas hacer algo extra:
    /*
    public function delete(): bool
    {
        Session::checkToken() or die(Text::_('JINVALID_TOKEN'));

        // Tu lógica personalizada ANTES de borrar (si es necesaria)
        // ...

        $result = parent::delete(); // Llama al método delete() de AdminController

        // Tu lógica personalizada DESPUÉS de borrar (si es necesaria)
        // ...

        return $result;
    }
    */
}