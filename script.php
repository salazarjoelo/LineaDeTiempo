<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
// Añade más 'use' si los necesitas para lógica específica

/**
 * Installation script for the LineaDeTiempo component.
 */
class Com_LineadetiempoInstallerScript extends InstallerScript
{
    /**
     * Minimum Joomla version requirement for this component.
     * @var string
     */
    public $minimumJoomla = '5.0.0'; // Tipo 'string' ELIMINADO

    /**
     * Minimum PHP version requirement for this component.
     * @var string
     */
    public $minimumPhp = '8.1.0';    // Tipo 'string' ELIMINADO

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param   string  $type    The type of change (install, update or discover_install).
     * @param   InstallerAdapter $parent  The class calling this method.
     *
     * @return  bool    True on success.
     */
    public function preflight($type, $parent): bool
    {
        // $app = Factory::getApplication();
        // $app->enqueueMessage('Preflight check for ' . $type, 'message');
        // Aquí puedes añadir validaciones (ej. versión de PHP, otras extensiones)
        
        // Ejemplo de verificación de versión mínima de PHP más explícita si es necesario
        // if (!version_compare(PHP_VERSION, $this->minimumPhp, '>=')) {
        //     Factory::getApplication()->enqueueMessage(Text::sprintf('JLIB_INSTALLER_MINIMUM_PHP_VERSION_REQUIRED', $this->minimumPhp), 'error');
        //     return false;
        // }
        // Similar para minimumJoomla si es necesario
        // if (!version_compare(JVERSION, $this->minimumJoomla, '>=')) {
        //    Factory::getApplication()->enqueueMessage(Text::sprintf('COM_LINEADETIEMPO_ERROR_JOOMLA_VERSION_TOO_LOW', $this->minimumJoomla), 'error');
        //    return false;
        // }

        return true;
    }

    /**
     * Method to install the component.
     *
     * @param   InstallerAdapter $parent  The class calling this method.
     *
     * @return  bool    True on success.
     */
    public function install($parent): bool
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_INSTALL_SUCCESS_MSG'), 'message');
        // $parent->getParent()->setRedirectURL('index.php?option=com_lineadetiempo&view=items'); // Opcional: redirigir
        return true;
    }

    /**
     * Method to update the component.
     *
     * @param   InstallerAdapter $parent  The class calling this method.
     *
     * @return  bool    True on success.
     */
    public function update($parent): bool
    {
        Factory::getApplication()->enqueueMessage(Text::sprintf('COM_LINEADETIEMPO_UPDATE_SUCCESS_MSG', $parent->get('manifest')->version), 'message');
        // Lógica de actualización
        return true;
    }

    /**
     * Method to uninstall the component.
     *
     * @param   InstallerAdapter $parent  The class calling this method.
     *
     * @return  bool    True on success.
     */
    public function uninstall($parent): bool
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_UNINSTALL_SUCCESS_MSG'), 'message');
        // Lógica de desinstalación (ej. borrar carpetas o archivos no eliminados por el manifiesto)
        return true;
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param   string  $type    The type of change (install, update or discover_install).
     * @param   InstallerAdapter $parent  The class calling this method.
     *
     * @return  void
     */
    public function postflight($type, $parent): void
    {
        // $app = Factory::getApplication();
        // $app->enqueueMessage('Postflight action for ' . $type, 'message');
        // Acciones después de la instalación/actualización, como limpiar caché específica.
    }
}