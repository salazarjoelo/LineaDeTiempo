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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// Obtener el usuario y los permisos para acciones en la lista
$user             = Factory::getApplication()->getIdentity();
$listOrder        = $this->escape($this->state->get('list.ordering'));
$listDirn         = $this->escape($this->state->get('list.direction'));
$canChangeState   = $user->authorise('core.edit.state', 'com_timeline'); // Permiso general para cambiar estado en el componente
// $canOrder      = $user->authorise('core.edit.state', 'com_timeline'); // O un permiso más específico para ordenar
// $saveOrder       = $listOrder === 'a.ordering'; // Habilitar reordenamiento si se ordena por 'ordering'

// if ($saveOrder && count($this->items) > 0) {
//     // Script para reordenar con drag & drop (si implementas esta funcionalidad)
//     // HTMLHelper::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), Route::_('index.php?option=com_timeline&task=items.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1'));
// }
?>

<form action="<?php echo Route::_('index.php?option=com_timeline&view=items'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2 col-md-2"> <?php // Clases Bootstrap si tu plantilla las usa ?>
            <?php echo $this->sidebar; // Renderiza la barra lateral (filtros) ?>
        </div>
        <div id="j-main-container" class="span10 col-md-10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif; ?>

        <?php
        // Barra de búsqueda y herramientas de filtro
        // Esto asume que $this->filterForm y $this->activeFilters están disponibles desde la Vista
        echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this, 'options' => ['filtersHidden' => false]]);
        ?>

        <?php if (empty($this->items)) : ?>
            <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped table-hover" id="itemList">
                <caption class="visually-hidden">
                    <?php echo Text::_('COM_TIMELINE_ITEMS_TABLE_CAPTION'); ?>,
                    <span id="orderedBy"><?php echo Text::sprintf('JGLOBAL_SORTED_BY', $this->escape($listOrder)); ?></span>,
                    <span id="filteredBy"><?php // Puedes añadir lógica para mostrar filtros activos si es necesario ?></span>
                </caption>
                <thead>
                    <tr>
                        <th scope="col" style="width:1%" class="text-center">
                            <?php echo HTMLHelper::_('grid.checkall'); ?>
                        </th>
                        <th scope="col" style="width:5%" class="text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMELINE_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width:15%" class="d-none d-md-table-cell"> <?php // Oculto en móviles, visible en md y superior ?>
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_TIMELINE_HEADING_DATE', 'a.date', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width:10%" class="d-none d-lg-table-cell"> <?php // Oculto en md, visible en lg y superior ?>
                            <?php echo HTMLHelper::_('searchtools.sort', 'JAUTHOR', 'author_name', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width:10%" class="d-none d-lg-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JDATE_CREATED', 'a.created', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width:5%" class="d-none d-md-table-cell text-center">
                            <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->items as $i => $item) :
                        // Permisos por ítem (si los tienes configurados en access.xml y los cargas en el modelo)
                        $canEditItem        = $user->authorise('core.edit', 'com_timeline.item.' . (int) $item->id);
                        // $canEditOwnItem  = $user->authorise('core.edit.own', 'com_timeline.item.' . (int) $item->id) && $item->created_by == $user->id;
                        $canChangeItemState = $user->authorise('core.edit.state', 'com_timeline.item.' . (int) $item->id);
                        if (!$canChangeItemState) { // Si no puede cambiar estado del ítem, usa el permiso general del componente
                            $canChangeItemState = $canChangeState;
                        }
                    ?>
                    <tr class="row<?php echo $i % 2; ?>" data-pk="<?php echo (int) $item->id; ?>">
                        <td class="text-center">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="text-center">
                            <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'items.', $canChangeItemState, 'cb_'); ?>
                        </td>
                        <td class="has-context">
                            <div class="break-word">
                                <?php if ($item->checked_out && $item->checked_out != $user->id) : ?>
                                    <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out_user_name ?? Text::_('JLIB_ iemand'), $item->checked_out_time, 'items.', false); ?>
                                <?php // El 'false' al final indica que no se puede hacer checkin (a menos que seas admin o tengas permiso) ?>
                                <?php endif; ?>

                                <?php if ($canEditItem /*|| $canEditOwnItem*/) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_timeline&task=item.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                        <?php echo $this->escape($item->title); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->title); ?>
                                <?php endif; ?>
                                <span class="small d-block">
                                    <?php // Aquí un extracto de la descripción o algún otro dato breve
                                    // echo Text::sprintf('COM_TIMELINE_ITEM_DESCRIPTION_PREVIEW', $this->escape(substr(strip_tags((string)$item->description), 0, 70) . '...')); ?>
                                </span>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('date', $item->date, Text::_('DATE_FORMAT_LC4')); ?>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <?php echo $this->escape($item->author_name ?? Text::_('JNONE')); // author_name viene del JOIN en el modelo ?>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <?php echo HTMLHelper::_('date', $item->created, Text::_('DATE_FORMAT_LC4')); ?>
                        </td>
                        <td class="d-none d-md-table-cell text-center">
                            <?php echo (int) $item->id; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php // Paginación
            echo LayoutHelper::render('joomla.pagination.links', ['view' => $this]);
            ?>
        <?php endif; ?>

        <input type="hidden" name="task" value=""> <?php // La tarea se establece por JS desde los botones de la toolbar ?>
        <input type="hidden" name="boxchecked" value="0"> <?php // Contador de checkboxes seleccionados ?>
        <?php echo HTMLHelper::_('form.token'); // Token CSRF para seguridad ?>
    </div>
</form>
