<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication; // Usado en la instanciación del Componente
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// Asegúrate que el namespace y la clase del Dispatcher sean correctos
use Salazarjoelo\Component\LineaDeTiempo\Administrator\Dispatcher\TimelineComponent;

/**
 * Joomla Service Provider for the LineaDeTiempo component (Administrator)
 *
 * @since  1.0.0
 */
class Provider implements ServiceProviderInterface
{
    /**
     * Registers service providers.
     *
     * @param   Container  $container  The DI container.
     *
     * @return  void
     *
     * @since   1.0.0
     */
    public function register(Container $container): void
    {
        // Registrar la fábrica MVC (MVCFactory)
        // Esta fábrica se usará para crear instancias de tus Modelos, Vistas y Controladores.
        $container->set(
            MVCFactoryInterface::class,
            function (Container $container) {
                $factory = new MVCFactory($container);
                // Define el namespace base para tus clases MVC del backend.
                // Joomla buscará clases en:
                // Salazarjoelo\Component\LineaDeTiempo\Administrator\Controller\*
                // Salazarjoelo\Component\LineaDeTiempo\Administrator\Model\*
                // Salazarjoelo\Component\LineaDeTiempo\Administrator\View\*
                // Salazarjoelo\Component\LineaDeTiempo\Administrator\Table\*
                $factory->setNamespace('Salazarjoelo\\Component\\LineaDeTiempo\\Administrator');
                return $factory;
            }
        );

        // Registrar la fábrica del Despachador del Componente (ComponentDispatcherFactory)
        // Esta fábrica se usará para crear la instancia principal de tu componente que maneja la solicitud.
        $container->set(
            ComponentDispatcherFactoryInterface::class,
            function (Container $container) {
                $factory = new ComponentDispatcherFactory($container);
                // Define el namespace para tu clase Dispatcher (o Extensión) principal del backend.
                // Joomla buscará: Salazarjoelo\Component\LineaDeTiempo\Administrator\Dispatcher\TimelineComponent
                $factory->setNamespace('Salazarjoelo\\Component\\LineaDeTiempo\\Administrator\\Dispatcher');
                return $factory;
            }
        );

        // Registrar la clase principal de tu Componente (el Dispatcher)
        // Esto permite que Joomla cree una instancia de tu componente cuando se accede a él.
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new TimelineComponent( // Tu clase Dispatcher: Salazarjoelo\Component\LineaDeTiempo\Administrator\Dispatcher\TimelineComponent
                    $container->get(CMSApplication::class),    // Inyecta la aplicación CMS
                    $container->get(MVCFactoryInterface::class) // Inyecta la fábrica MVC que acabamos de configurar
                    // El basePath usualmente es autodetectado por MVCComponent para el backend (JPATH_COMPONENT_ADMINISTRATOR)
                );
                return $component;
            }
        );
    }
}