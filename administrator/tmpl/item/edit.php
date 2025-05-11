<?php
/**
 * @package     Salazarjoelo\Component\LineaDeTiempo
 * @subpackage  com_lineadetiempo
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Variables de la Vista (HtmlView)
// $this->form (objeto JForm)
// $this->item (datos del ítem)
// $this->state (estado del modelo)

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator'); // O 'behavior.formvalidation' si usas validación nativa BS5
HTMLHelper::_('bootstrap.tab'); // Para el sistema de pestañas de Joomla
?>

<form action="<?php echo Route::_('index.php?option=com_lineadetiempo&layout=edit&id=' . (isset($this->item->id) ? (int) $this->item->id : 0)); ?>"
    method="post" name="adminForm" id="item-form" class="form-validate">

    <?php // Puedes usar este layout para mostrar pestañas automáticamente si tu formulario tiene múltiples fieldsets
          // o renderizar fieldsets individualmente como en el ejemplo de abajo.
    /*
    echo LayoutHelper::render(
        'joomla.edit.form',
        [
            'form'                 => $this->form,
            'data'                 => $this->item,
            ' ব্যাটিংHookElement' => 'botones-extra-aqui', // Opcional
            'tabNavigationId'      => 'myTabSetID' // Opcional, para control de pestañas
        ]
    );
    */
    ?>

    <?php // Renderizado manual de pestañas y fieldsets para mayor control ?>
    <div class="main-card">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'itemTabSet', ['active' => 'details', 'recall' => true, 'breakpoint' => 768]); ?>

        <?php // Pestaña de Detalles ?>
        <?php echo HTMLHelper::_('uitab.addTab', 'itemTabSet', 'details', Text::_($this->form->getFieldset('details')->label ?: 'COM_LINEADETIEMPO_ITEM_DETAILS_TAB_LABEL')); ?>
            <div class="row">
                <div class="col-md-9"> <?php // Columna principal para campos ?>
                    <fieldset class="options-form">
                        <?php echo $this->form->renderFieldset('details'); ?>
                    </fieldset>
                </div>
                <div class="col-md-3"> <?php // Columna lateral para campos como estado, etc. (si los pones en un fieldset separado) ?>
                    <?php // Ejemplo: Renderizar campos individuales si no están en 'details'
                        // echo $this->form->renderField('state');
                        // echo $this->form->renderField('ordering'); // Usualmente se oculta o es readonly
                    ?>
                </div>
            </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php // Pestaña de Metadatos (si existe el fieldset 'metadata' en item.xml) ?>
        <?php if ($this->form->getFieldset('metadata')) : ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'itemTabSet', 'metadata', Text::_($this->form->getFieldset('metadata')->label ?: 'JGLOBAL_FIELDSET_METADATA_OPTIONS')); ?>
                <fieldset class="options-form">
                    <?php echo $this->form->renderFieldset('metadata'); ?>
                </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>

        <?php // Pestaña de Permisos (si existe el fieldset 'item_permissions' y el usuario tiene permisos) ?>
        <?php if ($this->form->getFieldset('item_permissions') && Factory::getUser()->authorise('core.admin', 'com_lineadetiempo')) : ?>
            <?php echo HTMLHelper::_('uitab.addTab', 'itemTabSet', 'permissions', Text::_($this->form->getFieldset('item_permissions')->label ?: 'JCONFIG_PERMISSIONS_LABEL')); ?>
                <fieldset class="options-form">
                    <?php echo $this->form->renderFieldset('item_permissions'); ?>
                </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>
        <?php endif; ?>
        
        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
    </div>

    <input type="hidden" name="task" value="">
    <?php echo HTMLHelper::_('form.token'); // Token CSRF ?>
</form>