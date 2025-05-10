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

class Com_LineadetiempoInstallerScript extends InstallerScript
{
    public $minimumJoomla = '5.0.0'; // Sin tipado 'string'
    public $minimumPhp = '8.1.0';    // Sin tipado 'string'

    public function install($parent): bool
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_INSTALL_SUCCESS_MSG'), 'message');
        return true;
    }

    public function uninstall($parent): bool
    {
        Factory::getApplication()->enqueueMessage(Text::_('COM_LINEADETIEMPO_UNINSTALL_SUCCESS_MSG'), 'message');
        return true;
    }

    public function update($parent): bool
    {
        Factory::getApplication()->enqueueMessage(Text::sprintf('COM_LINEADETIEMPO_UPDATE_SUCCESS_MSG', $parent->get('manifest')->version), 'message');
        return true;
    }

    public function preflight($type, $parent): bool
    {
        return true;
    }

    public function postflight($type, $parent): void
    {
    }
}