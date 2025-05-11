<?php
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$user = JFactory::getUser();
?>
<div class="linea-tiempo">
    <?php if ($user->authorise('core.create', 'com_lineadetiempo')) : ?>
        <a href="<?php echo Route::_('index.php?option=com_lineadetiempo&task=timeline.add'); ?>" class="btn btn-primary">
            <?php echo Text::_('COM_LINEADETIEMPO_ADD_EVENT'); ?>
        </a>
    <?php endif; ?>

    <?php foreach ($this->items as $item) : ?>
        <div class="evento">
            <h3><?php echo $item->titulo; ?></h3>
            <p><?php echo $item->descripcion; ?></p>
            
            <?php if ($user->authorise('core.edit', 'com_lineadetiempo')) : ?>
                <a href="<?php echo Route::_('index.php?option=com_lineadetiempo&task=timeline.edit&id=' . $item->id); ?>">
                    <?php echo Text::_('COM_LINEADETIEMPO_EDIT_EVENT'); ?>
                </a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>