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

use Joomla\CMS\MVC\Controller\BaseController;
// Si necesitaras Factory o Text aquí, deberías añadirlos con 'use'.

/**
 * Default Display Controller for LineaDeTiempo component (Administrator)
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

    // La clase BaseController ya tiene un método display() funcional que
    // cargará la vista especificada en $default_view (o la vista 'view' de la URL)
    // y llamará a su método display(). No es necesario sobrescribirlo aquí
    // a menos que tengas una lógica de pre-visualización muy específica.
}