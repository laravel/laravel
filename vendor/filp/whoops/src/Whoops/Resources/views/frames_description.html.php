<div class="frames-description <?php echo $has_frames_tabs ? 'frames-description-application' : '' ?>">
  <?php if ($has_frames_tabs): ?>
    <a href="#" id="application-frames-tab" class="frames-tab <?php echo $active_frames_tab == 'application' ? 'frames-tab-active' : '' ?>">
        Application frames (<?php echo $frames->countIsApplication() ?>)
    </a>
    <a href="#" id="all-frames-tab" class="frames-tab <?php echo $active_frames_tab == 'all' ? 'frames-tab-active' : '' ?>">
      All frames (<?php echo count($frames) ?>)
    </a>
  <?php else: ?>
    <span>
        Stack frames (<?php echo count($frames) ?>)
    </span>
  <?php endif; ?>
</div>
