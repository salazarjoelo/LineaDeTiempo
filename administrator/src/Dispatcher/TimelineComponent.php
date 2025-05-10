<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;

/**
 * Administrator-side main entry point for Timeline component.
 *
 * @since  1.0.0
 */
class TimelineComponent extends MVCComponent
{
    /**
     * Constructor.
     *
     * @param   CMSApplication       $application  The CMS Application.
     * @param   MVCFactoryInterface  $factory      The MVC factory.
     * @param   ?string              $basePath     The base path for the component.
     *
     * @since   1.0.0
     */
    public function __construct(CMSApplication $application, MVCFactoryInterface $factory, ?string $basePath = null)
    {
        // El basePath por defecto para el backend usualmente es JPATH_COMPONENT_ADMINISTRATOR
        // MVCComponent puede intentar autodetectarlo, pero puedes especificarlo si es necesario.
        // Para la estructura administrator/src/Dispatcher, el basePath para las clases MVC
        // (Controller, Model, View) sería dirname(__DIR__), asumiendo que este archivo
        // está en administrator/src/Dispatcher/ y las otras carpetas MVC están al mismo nivel que Dispatcher.
        // Sin embargo, el MVCFactory se encarga de esto basado en los namespaces.
        
        parent::__construct($application, $factory, $basePath);
    }
    
    // La clase base MVCComponent ya maneja la obtención del controlador
    // basado en la tarea (task) o la vista (view) de la solicitud.
    // Solo necesitas sobrescribir getController() si tienes una lógica muy específica
    // para determinar el controlador, lo cual es raro para empezar.
}