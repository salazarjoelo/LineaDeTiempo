<div class="timeline-item <?php echo $this->item->media_type; ?>-item">
    <?php if ($this->item->media_type === 'image') : ?>
        <figure class="responsive-media">
            <img 
                src="<?php echo htmlspecialchars($this->item->media_file); ?>" 
                alt="<?php echo htmlspecialchars($this->item->title); ?>"
                loading="lazy"
                srcset="
                    <?php echo $this->item->media_file; ?> 1200w,
                    <?php echo $this->item->media_file; ?>?width=800 800w,
                    <?php echo $this->item->media_file; ?>?width=400 400w
                "
                sizes="(max-width: 768px) 100vw, 50vw"
            >
        </figure>
    <?php elseif ($this->item->media_type === 'video') : ?>
        <div class="video-container responsive-media">
            <?php echo $this->getVideoPlayer($this->item->video_url); ?>
        </div>
    <?php elseif ($this->item->media_type === 'embed') : ?>
        <div class="embed-container responsive-media">
            <?php echo $this->sanitizeEmbed($this->item->embed_code); ?>
        </div>
    <?php endif; ?>
</div>