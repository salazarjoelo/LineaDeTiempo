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
use Joomla\CMS\Session\Session;

// Variables de la Vista (HtmlView)
// $this->items
// $this->pagination
// $this->state (contiene list.ordering, list.direction, etc.)
// $this->filterForm (para los filtros)
// $this->activeFilters
// $this->sidebar (si lo generaste en la vista)

$user             = Factory::getApplication()->getIdentity();
$listOrder        = $this->escape($this->state->get('list.ordering'));
$listDirn         = $this->escape($this->state->get('list.direction'));
$canChangeState   = $user->authorise('core.edit.state', 'com_lineadetiempo');
// $canOrder      = $user->authorise('core.edit.state', 'com_lineadetiempo'); // O permiso específico
// $saveOrder       = $listOrder === 'a.ordering';

// if ($saveOrder && count($this->items ?? []) > 0) {
//     HTMLHelper::_('sortablelist.sortable', 'itemList', 'adminForm', strtolower($listDirn), Route::_('index.php?option=com_lineadetiempo&task=items.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1'));
// }
?>

<form action="<?php echo Route::_('index.php?option=com_lineadetiempo&view=items'); ?>" method="post" name="adminForm" id="adminForm">
    <?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2 col-md-2">
            <?php echo $this->sidebar; ?>
        </div>
        <div id="j-main-container" class="span10 col-md-10">
    <?php else : ?>
        <div id="j-main-container">
    <?php endif; ?>

        <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this, 'options' => ['filtersHidden' => false]]); ?>

        <?php if (empty($this->items)) : ?>
            <div class="alert alert-info">
                <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
            </div>
        <?php else : ?>
            <table class="table table-striped table-hover" id="itemList">
                <caption class="visually-hidden">
                    <?php echo Text::_('COM_LINEADETIEMPO_ITEMS_TABLE_CAPTION'); ?>,
                    <span id="orderedBy"><?php echo Text::sprintf('JGLOBAL_SORTED_BY', $this->escape($listOrder)); ?></span>
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
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_LINEADETIEMPO_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width:15%" class="d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('searchtools.sort', 'COM_LINEADETIEMPO_HEADING_DATE', 'a.date', $listDirn, $listOrder); ?>
                        </th>
                        <th scope="col" style="width:10%" class="d-none d-lg-table-cell">
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
                        $canEditItem        = $user->authorise('core.edit', 'com_lineadetiempo.item.' . (int) $item->id);
                        $canChangeItemState = $user->authorise('core.edit.state', 'com_lineadetiempo.item.' . (int) $item->id);
                        // Si no puede cambiar el estado del ítem, usar el permiso general del componente si lo tiene
                        if (!$canChangeItemState) {
                            $canChangeItemState = $canChangeState;
                        }
                    ?>
                    <tr class="row<?php echo $i % 2; ?>" data-pk="<?php echo (int) $item->id; ?>">
                        <td class="text-center">
                            <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                        </td>
                        <td class="text-center">
                            <?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'items.', $canChangeItemState, 'cb'); ?>
                        </td>
                        <td class="has-context">
                            <div>
                                <?php if ($item->checked_out && $item->checked_out != $user->id) : ?>
                                    <?php echo HTMLHelper::_('jgrid.checkedout', $i, $item->checked_out_user_name ?? Text::_('JLIB_ SOMEONE'), $item->checked_out_time, 'items.', false); ?>
                                <?php endif; ?>
                                <?php if ($canEditItem) : ?>
                                    <a href="<?php echo Route::_('index.php?option=com_lineadetiempo&task=item.edit&id=' . (int) $item->id); ?>" title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                        <?php echo $this->escape($item->title); ?>
                                    </a>
                                <?php else : ?>
                                    <?php echo $this->escape($item->title); ?>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <?php echo HTMLHelper::_('date', $item->date, Text::_('COM_LINEADETIEMPO_DATE_FORMAT_LIST')); ?>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            <?php echo $this->escape($item->author_name ?? Text::_('JNONE')); ?>
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
            <?php echo LayoutHelper::render('joomla.pagination.links', ['view' => $this]); ?>
        <?php endif; ?>
        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>