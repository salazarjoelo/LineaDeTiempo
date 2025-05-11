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
use Joomla\CMS\Installer\Adapter\AdapterInterface; // Para el docblock y claridad

/**
 * Installation script for the LineaDeTiempo component.
 */
class Com_LineadetiempoInstallerScript extends InstallerScript
{
    public $minimumJoomla = '5.0.0';
    public $minimumPhp = '8.1.0';

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param   string            $type    The type of change (install, update or discover_install).
     * @param   AdapterInterface  $parent  The class calling this method.
     *
     * @return  bool    True on success. (El padre devuelve true, así que nosotros también)
     */
    public function preflight($type, $parent) // SIN type hints en parámetros, SIN tipo de retorno
    {
        return true;
    }

    /**
     * Method to install the component.
     *
     * @param   AdapterInterface  $parent  The class calling this method.
     *
     * @return  bool    True on success.
     */
    public function install($parent) // SIN type hints en parámetros, SIN tipo de retorno
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_INSTALL_SUCCESS_MSG'), 'message');
        return true;
    }

    /**
     * Method to update the component.
     *
     * @param   AdapterInterface  $parent  The class calling this method.
     *
     * @return  bool    True on success.
     */
    public function update($parent) // SIN type hints en parámetros, SIN tipo de retorno
    {
        $manifest = $parent->getManifest();
        $version = isset($manifest->version) ? (string) $manifest->version : Text::_('JUNKNOWN');

        Factory::getApplication()->enqueueMessage(Text::sprintf('COM_LINEADETIEMPO_UPDATE_SUCCESS_MSG', $version), 'message');
        return true;
    }

    /**
     * Method to uninstall the component.
     *
     * @param   AdapterInterface  $parent  The class calling this method.
     *
     * @return  bool    True on success.
     */
    public function uninstall($parent) // SIN type hints en parámetros, SIN tipo de retorno
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_UNINSTALL_SUCCESS_MSG'), 'message');
        return true;
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param   string            $type    The type of change (install, update or discover_install).
     * @param   AdapterInterface  $parent  The class calling this method.
     *
     * @return  void (El padre no especifica, pero no devuelve nada explícitamente más que true en algunos casos)
     * Para ser seguros y compatibles, no especificamos tipo de retorno.
     */
    public function postflight($type, $parent) // SIN type hints en parámetros, SIN tipo de retorno
    {
        // Acciones después de la instalación/actualización.
        // El método padre postflight devuelve void (o no tiene return type, que es similar a void en PHP < 7.1).
        // Si queremos ser consistentes, no retornamos nada o retornamos true si el padre lo hiciera.
        // La clase InstallerScript base tiene un postflight que retorna true,
        // pero no tiene un tipo de retorno declarado. Así que es mejor no ponerlo.
    }
}