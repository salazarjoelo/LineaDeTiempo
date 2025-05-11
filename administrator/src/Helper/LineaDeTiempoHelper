<?php
declare(strict_types=1);

namespace Salazarjoelo\Component\LineaDeTiempo\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;

class LineaDeTiempoHelper // Nombre de clase actualizado
{
    public static function addSubmenu(string $viewName): void
    {
        // Lógica si es necesaria, aunque los submenús del manifiesto son suficientes
    }

    public static function getActions(int $itemId = 0): \Joomla\CMS\Object\CMSObject
    {
        // ContentHelper::getActions('com_lineadetiempo', 'item', $itemId) es preferible en J5
        return ContentHelper::getActions('com_lineadetiempo', 'item', $itemId);
    }
}