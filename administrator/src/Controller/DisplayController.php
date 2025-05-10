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

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;

/**
 * Default Display Controller for Timeline component (Administrator)
 *
 * @since  1.0.0
 */
class DisplayController extends BaseController
{
    /**
     * The default view for the display method.
     *
     * @var    string
     * @since  1.0.0
     */
    protected $default_view = 'items'; // Por defecto, mostrar la vista de lista 'items'

    /**
     * Method to display a view.
     *
     * @param   boolean  $cachable   If true, the view output will be cached.
     * @param   array    $urlparams  An array of safe url parameters and their variable types,
     * for valid values see {@link \Joomla\Filter\InputFilter::$tags}.
     *
     * @return  BaseController  This object to support chaining.
     *
     * @since   1.0.0
     */
    public function display($cachable = false, $urlparams = []): BaseController // Tipado de retorno ajustado
    {
        // La clase base BaseController::display ya se encarga de cargar la vista
        // (basada en $default_view o el parámetro 'view' en la URL)
        // y llamar a su método display().
        
        // No necesitas añadir lógica aquí a menos que quieras hacer algo
        // muy específico antes de que la vista se muestre, como comprobaciones de permisos globales
        // para acceder al componente, aunque eso se maneja mejor con access.xml y el dispatcher.
        
        // Ejemplo: Redirigir si no tiene acceso al componente (esto es más un ejemplo,
        // el sistema de ruteo y ACL de Joomla debería manejar esto antes)
        // $user = Factory::getApplication()->getIdentity();
        // if (!$user->authorise('core.manage', 'com_timeline')) {
        //    Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
        //    Factory::getApplication()->redirect(Route::_('index.php'));
        //    return $this;
        // }

        return parent::display($cachable, $urlparams);
    }
}