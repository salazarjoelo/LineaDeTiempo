<?php
defined('_JEXEC') or die;

class Com_LineadetiempoInstallerScript
{
    public function install($parent)
    {
        JFactory::getApplication()->enqueueMessage(
            JText::_('COM_LINEADETIEMPO_INSTALL_SUCCESS'), 
            'success'
        );
    }

    public function uninstall($parent)
    {
        JFactory::getApplication()->enqueueMessage(
            JText::_('COM_LINEADETIEMPO_UNINSTALL_SUCCESS'), 
            'success'
        );
    }

    public function update($parent)
    {
        $this->install($parent);
    }
}