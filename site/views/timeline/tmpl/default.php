<?php
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die;

HTMLHelper::_('behavior.core');
HTMLHelper::_('jquery.framework');

// Web Assets
$wa = $this->document->getWebAssetManager();
$wa->registerAndUseStyle('com_lineadetiempo.frontend', 'com_lineadetiempo/frontend.css');
$wa->registerAndUseScript('com_lineadetiempo.frontend', 'com_lineadetiempo/frontend.js');

// Datos para JS
$this->document->addScriptOptions('com_lineadetiempo', [
    'apiBase' => JUri::root() . 'index.php?option=com_lineadetiempo',
    'csrfToken' => JSession::getFormToken()
]);

// Control de acceso
$user = Factory::getUser();
$canEdit = $user->authorise('core.edit', 'com_lineadetiempo');
?>

<div class="linea-tiempo-container" data-component="timeline">
    <?php foreach ($this->items as $item) : ?>
        <article class="evento" 
                 data-id="<?php echo $item->id; ?>" 
                 data-social="<?php echo $item->red_social; ?>">
            
            <header class="evento-header">
                <h3><?php echo htmlspecialchars($item->titulo, ENT_QUOTES, 'UTF-8'); ?></h3>
                <?php if ($canEdit) : ?>
                    <div class="evento-actions">
                        <a href="<?php echo JRoute::_('index.php?option=com_lineadetiempo&task=timeline.edit&id=' . $item->id); ?>" 
                           class="btn btn-sm btn-edit">
                            <?php echo Text::_('JACTION_EDIT'); ?>
                        </a>
                    </div>
                <?php endif; ?>
            </header>
            
            <div class="evento-content">
                <?php if ($item->imagen) : ?>
                    <figure class="evento-image">
                        <img src="<?php echo htmlspecialchars($item->imagen, ENT_QUOTES, 'UTF-8'); ?>" 
                             alt="<?php echo htmlspecialchars($item->titulo, ENT_QUOTES, 'UTF-8'); ?>"
                             loading="lazy">
                    </figure>
                <?php endif; ?>
                
                <div class="evento-description">
                    <?php echo nl2br(htmlspecialchars($item->descripcion, ENT_QUOTES, 'UTF-8')); ?>
                </div>
                
                <?php if ($item->red_social && $item->url_redsocial) : ?>
                    <div class="evento-social">
                        <?php echo $this->loadTemplate('social_' . $item->red_social); ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <footer class="evento-meta">
                <time datetime="<?php echo $item->fecha; ?>">
                    <?php echo HTMLHelper::_('date', $item->fecha, Text::_('DATE_FORMAT_LC4')); ?>
                </time>
            </footer>
        </article>
    <?php endforeach; ?>
</div>