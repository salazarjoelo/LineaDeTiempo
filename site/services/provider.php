<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Site\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// Asegúrate que la ruta al Dispatcher (TimelineComponent) del frontend sea correcta
use Salazarjoelo\Component\Timeline\Site\Dispatcher\TimelineComponent;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\SiteApplication; // Añadido para el Router
use Joomla\CMS\Menu\Menu;                   // Añadido para el Router
use Joomla\Database\DatabaseInterface;      // Añadido para el Router
use Joomla\CMS\Component\Router\RouterInterface; // Añadido para el Router
use Salazarjoelo\Component\Timeline\Site\Router\Router as TimelineRouter; // Añadido para el Router


/**
 * Joomla Service Provider for the Timeline component (Site)
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
        // Registrar la fábrica MVC
        $container->set(
            MVCFactoryInterface::class,
            function (Container $container) {
                $factory = new MVCFactory($container);
                // Define el namespace base para tus clases MVC del frontend
                // Joomla buscará en Salazarjoelo\Component\Timeline\Site\Controller, Model, View
                $factory->setNamespace('Salazarjoelo\\Component\\Timeline\\Site');
                return $factory;
            }
        );

        // Registrar la fábrica del despachador del componente
        $container->set(
            ComponentDispatcherFactoryInterface::class,
            function (Container $container) {
                $factory = new ComponentDispatcherFactory($container);
                // El namespace aquí es para la clase *Dispatcher\TimelineComponent* del frontend
                $factory->setNamespace('Salazarjoelo\\Component\\Timeline\\Site\\Dispatcher');
                return $factory;
            }
        );

        // Registrar la clase principal del componente (Dispatcher)
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new TimelineComponent(
                    $container->get(CMSApplication::class), // CMSApplication es más genérico que SiteApplication aquí
                    $container->get(MVCFactoryInterface::class)
                    // , JPATH_COMPONENT_SITE
                );
                return $component;
            }
        );

        // NUEVO INICIO: Registrar el Router del componente
        $container->share(
            RouterInterface::class, // Interfaz que implementa tu Router
            function (Container $container) {
                // El Router necesita la aplicación del sitio, el menú y, a veces, la base de datos
                return new TimelineRouter( // Tu clase Router específica
                    $container->get(SiteApplication::class),
                    $container->get(Menu::class),
                    $container->get(DatabaseInterface::class)
                );
            },
            true, // 'true' significa que es un servicio compartido (singleton)
            'com_timeline' // Un alias para referenciar específicamente el router de este componente si es necesario
        );
        // NUEVO FIN
    }
}