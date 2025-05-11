<?php defined('_JEXEC') or die; ?>
<link rel="stylesheet" href="https://cdn.knightlab.com/libs/timeline3/latest/css/timeline.css">
<script src="https://cdn.knightlab.com/libs/timeline3/latest/js/timeline.js"></script>

<div id="timeline-embed" style="width: 100%; height: 600px;"></div>

<script>
  window.timeline = new TL.Timeline(
    'timeline-embed',
    '<?php echo JUri::root(); ?>index.php?option=com_lineadetiempo&view=timeline&format=json',
    { initial_zoom: 2 }
  );
</script>
