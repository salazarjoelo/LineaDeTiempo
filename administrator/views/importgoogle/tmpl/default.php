<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('stylesheet', 'com_lineadetiempo/google-import.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'com_lineadetiempo/google-import.js', ['version' => 'auto', 'relative' => true]);
?>
<div class="google-import-container">
    <form action="<?php echo Route::_('index.php?option=com_lineadetiempo&task=importgoogle.search'); ?>">
        <input type="text" name="query" placeholder="<?php echo Text::_('COM_LINEADETIEMPO_GOOGLE_SEARCH_PLACEHOLDER'); ?>">
        <button type="submit"><?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button>
    </form>

    <div class="search-results-grid">
        <?php foreach ($this->items as $item) : ?>
            <div class="search-result-item" data-json='<?php echo json_encode($item); ?>'>
                <?php if (isset($item['pagemap']['cse_image'][0]['src'])) : ?>
                    <img src="<?php echo $item['pagemap']['cse_image'][0]['src']; ?>" loading="lazy">
                <?php endif; ?>
                <h4><?php echo $item['title']; ?></h4>
                <p><?php echo $item['snippet']; ?></p>
                <button class="select-item"><?php echo Text::_('JSELECT'); ?></button>
            </div>
        <?php endforeach; ?>
    </div>
</div>