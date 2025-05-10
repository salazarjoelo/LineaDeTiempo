<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper; // Para renderizar el formulario
use Joomla\CMS\Router\Route;

// Cargar comportamientos de Joomla (validación, keepalive, pestañas)
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator'); // O 'behavior.formvalidation' para validación nativa de BS5
HTMLHelper::_('bootstrap.tab'); // Para las pestañas (si las usas)

// Si tienes scripts JS específicos para este formulario, cárgalos aquí o en la Vista
// $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
// $wa->useScript('com_timeline.admin.item-form'); // Ejemplo
?>

<form action="<?php echo Route::_('index.php?option=com_timeline&layout=edit&id=' . (isset($this->item->id) ? (int) $this->item->id : 0)); ?>"
    method="post" name="adminForm" id="item-form" class="form-validate"> <?php // class="form-validate" para la validación JS de Joomla ?>

    <?php // Renderizar el título y el campo de alias si los tuvieras (común en artículos, etc.) ?>
    <?php // echo LayoutHelper::render('joomla.edit.title_alias', ['item' => $this->item, 'form' => $this->form]); ?>

    <div class="main-card"> <?php // Contenedor principal, puedes usar clases de Bootstrap si tu plantilla de admin las soporta bien ?>
    
        <?php echo HTMLHelper::_('uitab.startTabSet', 'myTabSetID', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'myTabSetID', 'details', Text::_($this->form->getFieldset('details')->label ?: 'COM_TIMELINE_ITEM_DETAILS_TAB_LABEL')); ?>
            <div class="row"> <?php // Usando un layout de Bootstrap de ejemplo ?>
                <div class="col-md-9">
                    <fieldset class="options-form"> <?php // O usa "adminform" si es el estilo de tu plantilla admin ?>
                        <?php echo $this->form->renderFieldset('details'); ?>
                    </fieldset>
                </div>
                <div class="col-md-3">
                    <?php // Aquí podrías renderizar otros campos o fieldsets específicos para la barra lateral del formulario
                        // Por ejemplo, el campo 'state' si no está en el fieldset 'details'.
                        // echo $this->form->renderField('state');
                    ?>
                </div>
            </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php // Pestaña de Metadatos (si el fieldset 'metadata' existe en tu item.xml) ?>
        <?php if ($this->form->getFieldset('metadata')) : ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTabSetID', 'metadata', Text::_($this->form->getFieldset('metadata')->label ?: 'JGLOBAL_FIELDSET_METADATA_OPTIONS')); ?>
                <fieldset class="options-form">
                    <?php echo $this->form->renderFieldset('metadata'); ?>
                </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>

        <?php // Pestaña de Permisos (si el fieldset 'item_permissions' existe y el usuario tiene permiso) ?>
        <?php if ($this->form->getFieldset('item_permissions') && Factory::getUser()->authorise('core.admin', 'com_timeline')) : ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'myTabSetID', 'permissions', Text::_($this->form->getFieldset('item_permissions')->label ?: 'JCONFIG_PERMISSIONS_LABEL')); ?>
                <fieldset class="options-form">
                    <?php echo $this->form->renderFieldset('item_permissions'); ?>
                </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>
        
        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

    </div> <?php // fin de .main-card ?>

    <input type="hidden" name="task" value=""> <?php // La tarea se establece por JS desde los botones de la toolbar ?>
    <?php echo HTMLHelper::_('form.token'); // Token CSRF para seguridad ?>
</form>
