<?php
defined('_JEXEC') or die;

class LineadetiempoHelper
{
    public static function addSubmenu($vName)
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_LINEADETIEMPO_SUBMENU_EVENTOS'),
            'index.php?option=com_lineadetiempo&view=eventos',
            $vName == 'eventos'
        );
    }

    public static function getSocialMediaOptions()
    {
        return [
            'twitter' => 'Twitter',
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'youtube' => 'YouTube'
        ];
    }
}