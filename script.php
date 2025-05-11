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
use Joomla\CMS\Installer\Adapter\AdapterInterface;

/**
 * Installation script for the LineaDeTiempo component.
 */
class Com_LineadetiempoInstallerScript extends InstallerScript
{
    public $minimumJoomla = '5.0.0';
    public $minimumPhp = '8.1.0';

    public function preflight($type, $parent) // Sin tipos en firma
    {
        return true;
    }

    public function install($parent) // Sin tipos en firma
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_INSTALL_SUCCESS_MSG'), 'message');
        return true;
    }

    public function update($parent) // Sin tipos en firma
    {
        $manifest = $parent->getManifest();
        $version = isset($manifest->version) ? (string) $manifest->version : Text::_('JUNKNOWN');
        Factory::getApplication()->enqueueMessage(Text::sprintf('COM_LINEADETIEMPO_UPDATE_SUCCESS_MSG', $version), 'message');
        return true;
    }

    public function uninstall($parent) // Sin tipos en firma
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_UNINSTALL_SUCCESS_MSG'), 'message');
        return true;
    }

    public function postflight($type, $parent) // Sin tipos en firma
    {
        // Acciones post-instalación/actualización si son necesarias
    }
}