<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\Service;

defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

// Asegúrate que la ruta al Dispatcher (TimelineComponent) sea correcta
use Salazarjoelo\Component\Timeline\Administrator\Dispatcher\TimelineComponent;

/**
 * Joomla Service Provider for the Timeline component (Administrator)
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
                // Define el namespace base para tus clases MVC del backend
                // Joomla buscará en Salazarjoelo\Component\Timeline\Administrator\Controller, Model, View
                $factory->setNamespace('Salazarjoelo\\Component\\Timeline\\Administrator');
                return $factory;
            }
        );

        // Registrar la fábrica del despachador del componente
        $container->set(
            ComponentDispatcherFactoryInterface::class,
            function (Container $container) {
                $factory = new ComponentDispatcherFactory($container);
                // El namespace aquí es para la clase *Dispatcher\TimelineComponent* del backend
                $factory->setNamespace('Salazarjoelo\\Component\\Timeline\\Administrator\\Dispatcher');
                return $factory;
            }
        );

        // Registrar la clase principal del componente (Dispatcher)
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new TimelineComponent(
                    $container->get(CMSApplication::class), // Pasa la aplicación
                    $container->get(MVCFactoryInterface::class) // Pasa la fábrica MVC
                    // El basePath se puede omitir para que MVCComponent lo autodetecte o puedes especificarlo
                    // , JPATH_COMPONENT_ADMINISTRATOR
                );
                return $component;
            }
        );
    }
}
