<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Site\Service; // Namespace correcto para esta ubicación

defined('_JEXEC') or die;

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\SiteApplication;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Menu\Menu;
use Joomla\Database\DatabaseInterface;
use Joomla\CMS\Component\Router\RouterInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

use Salazarjoelo\Component\LineaDeTiempo\Site\Dispatcher\TimelineComponent;
use Salazarjoelo\Component\LineaDeTiempo\Site\Router\Router as LineaDeTiempoRouter; // Alias para tu Router

/**
 * Joomla Service Provider for the LineaDeTiempo component (Site)
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
     */
    public function register(Container $container): void
    {
        // Registrar la fábrica MVC
        $container->set(
            MVCFactoryInterface::class,
            function (Container $container) {
                $factory = new MVCFactory($container);
                $factory->setNamespace('Salazarjoelo\\Component\\LineaDeTiempo\\Site');
                return $factory;
            }
        );

        // Registrar la fábrica del despachador del componente
        $container->set(
            ComponentDispatcherFactoryInterface::class,
            function (Container $container) {
                $factory = new ComponentDispatcherFactory($container);
                $factory->setNamespace('Salazarjoelo\\Component\\LineaDeTiempo\\Site\\Dispatcher');
                return $factory;
            }
        );

        // Registrar la clase principal del componente (Dispatcher)
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new TimelineComponent(
                    $container->get(CMSApplication::class),
                    $container->get(MVCFactoryInterface::class)
                );
                return $component;
            }
        );

        // Registrar el Router del componente
        $container->share(
            RouterInterface::class,
            function (Container $container) {
                return new LineaDeTiempoRouter(
                    $container->get(SiteApplication::class),
                    $container->get(Menu::class),
                    $container->get(DatabaseInterface::class)
                );
            },
            true,
            'com_lineadetiempo' // Alias para el router de este componente
        );
    }
}