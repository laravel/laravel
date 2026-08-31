<div class="mx-2 mb-1 mt-<?php echo $marginTop ?>">
    <span class="px-1 bg-<?php echo $bgColor ?> text-<?php echo $fgColor ?> uppercase"><?php echo $title ?></span>
    <span class="<?php if ($title) {
        echo 'ml-1';
    } ?>">
        <?php echo htmlspecialchars($content) ?>
    </span>
</div>
