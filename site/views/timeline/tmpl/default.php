<?php foreach ($this->items as $item) : ?>
    <div class="timeline-event">
        <?php if ($item->tipo_media === 'imagen') : ?>
            <img src="<?php echo $item->url_media; ?>" 
                 alt="<?php echo htmlspecialchars($item->titulo); ?>">
        <?php elseif ($item->tipo_media === 'video') : ?>
            <div class="video-wrapper">
                <iframe src="<?php echo $item->url_media; ?>" 
                        allowfullscreen></iframe>
            </div>
        <?php endif; ?>
        <div class="event-content">
            <h3><?php echo $item->titulo; ?></h3>
            <p><?php echo $item->descripcion; ?></p>
        </div>
    </div>
<?php endforeach; ?>