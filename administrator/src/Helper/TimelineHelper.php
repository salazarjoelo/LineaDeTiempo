<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

declare(strict_types=1);

namespace Salazarjoelo\Component\Timeline\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper; // Para permisos
use Joomla\CMS\Router\Route; // Si necesitas generar rutas

/**
 * Timeline Helper class for the administrator part of the component.
 *
 * @since  1.0.0
 */
class TimelineHelper
{
    /**
     * Configure the Linkbar.
     * A common use for helpers is to add submenus or quick icons if not using the main menu system.
     * Para Joomla 5, esto se maneja mejor con la definición de <submenu> en el manifiesto,
     * pero aquí un ejemplo de cómo se hacía o para lógica más compleja.
     *
     * @param   string  $viewName  The name of the active view.
     *
     * @return  void
     * @since   1.0.0
     */
    public static function addSubmenu(string $viewName): void
    {
        // Ejemplo, no siempre necesario si el manifiesto maneja bien los submenús.
        // \Joomla\CMS\Toolbar\ToolbarHelper::addSubmenu($viewName === 'nombre_de_vista_especial');

        // Ejemplo de cómo añadir un botón específico a la barra de submenú
        // if (Factory::getApplication()->getIdentity()->authorise('core.manage', 'com_timeline')) {
        //     \Joomla\CMS\Toolbar\ToolbarHelper::custom(
        //         'items.miTareaCustom', // task
        //         'cogs',                // icon
        //         'Cog ALT',             // alt text
        //         'Mi Acción Custom',    // title
        //         false                  // select list
        //     );
        // }
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @param   int  $itemId  The item ID. (Opcional, para permisos a nivel de ítem)
     *
     * @return  \Joomla\CMS\Object\CMSObject  An ACL object.
     * @since   1.0.0
     */
    public static function getActions(int $itemId = 0): \Joomla\CMS\Object\CMSObject
    {
        $user   = Factory::getApplication()->getIdentity();
        $assetName = 'com_timeline';

        if ($itemId) {
            $assetName .= '.item.' . $itemId;
        }

        // Para Joomla 5, ContentHelper::getActions es más directo y preferido
        // return ContentHelper::getActions($assetName);
        // El siguiente es el método más tradicional que también funciona:

        $actions = new \Joomla\CMS\Object\CMSObject; // O \Joomla\Registry\Registry

        $actions->set('core.admin', $user->authorise('core.admin', $assetName));
        $actions->set('core.manage', $user->authorise('core.manage', $assetName));
        $actions->set('core.create', $user->authorise('core.create', $assetName));
        $actions->set('core.edit', $user->authorise('core.edit', $assetName));
        $actions->set('core.edit.own', $user->authorise('core.edit.own', $assetName));
        $actions->set('core.edit.state', $user->authorise('core.edit.state', $assetName));
        $actions->set('core.delete', $user->authorise('core.delete', $assetName));

        return $actions;
    }

    // Puedes añadir más métodos estáticos de utilidad aquí.
    // Por ejemplo, para formatear fechas de una manera específica para tu componente,
    // o para generar URLs específicas, etc.
}