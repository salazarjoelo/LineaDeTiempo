<?php
/**
 * @package     Salazarjoelo\Component\Timeline
 * @subpackage  com_timeline
 *
 * @copyright   Copyright (C) 2023-2025 Joel Salazar. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

// $this->items, $this->pagination, $this->params, $this->state están disponibles desde la Vista
?>

<div class="com-timeline-items <?php echo $this->pageclass_sfx; ?>">

    <?php if ($this->params->get('show_page_heading')) : ?>
        <div class="page-header">
            <h1><?php echo $this->escape($this->params->get('page_heading', Text::_('COM_TIMELINE_DEFAULT_PAGE_TITLE'))); ?></h1>
        </div>
    <?php endif; ?>

    <?php if ($this->params->get('show_category_title', 1) && !empty($this->category->title)) : // Ejemplo si tuvieras categorías ?>
        <h2><?php echo $this->escape($this->category->title); ?></h2>
    <?php endif; ?>

    <?php if (empty($this->items)) : ?>
        <?php if ($this->params->get('show_no_articles', 1)) : ?>
            <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                <?php echo Text::_('COM_TIMELINE_NO_ITEMS_FOUND'); ?>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="timeline-list">
            <?php foreach ($this->items as $i => $item) : ?>
                <div class="timeline-item card mb-3"> <?php // Usando clases de Bootstrap 5 como ejemplo ?>
                    <div class="card-header">
                        <h3 class="card-title timeline-item-title">
                            <?php // Enlace al ítem individual si tienes una vista para ello ?>
                            <?php // <a href="<?php echo Route::_('index.php?option=com_timeline&view=item&id=' . (int) $item->id); ?>"> ?>
                                <?php echo $this->escape($item->title); ?>
                            <?php // </a> ?>
                        </h3>
                        <?php if (!empty($item->date) && $this->params->get('show_item_date', 1)) : ?>
                            <small class="timeline-item-date text-muted">
                                <?php echo HTMLHelper::_('date', $item->date, Text::_('COM_TIMELINE_DATE_FORMAT_SITE')); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    <div class="card-body timeline-item-body">
                        <?php if (!empty($item->description) && $this->params->get('show_item_description', 1)) : ?>
                            <div class="timeline-item-description">
                                <?php echo HTMLHelper::_('content.prepare', $item->description, '', 'com_timeline.items'); // Procesa plugins de contenido ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($item->author_name) && $this->params->get('show_item_author', 0)) : ?>
                        <div class="card-footer text-muted timeline-item-author">
                            <?php echo Text::sprintf('COM_TIMELINE_ITEM_AUTHOR_LABEL', $this->escape($item->author_name)); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($this->pagination->getNumPages() > 1 && $this->params->get('show_pagination', 1)) : ?>
            <div class="pagination-wrapper">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php endif; ?>

    <?php endif; ?>
</div>
