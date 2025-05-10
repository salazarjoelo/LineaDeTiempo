<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Site\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Default Display Controller for Timeline component (Site)
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
    protected $default_view = 'items'; // Asumiremos que quieres mostrar una lista de 'items' por defecto

    // La clase base BaseController::display() se encargará de cargar y mostrar la vista.
    // No es necesario sobrescribir el método display() aquí a menos que tengas lógica específica.
}