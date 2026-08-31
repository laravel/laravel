<div class="flex mx-2 max-w-150">
    <span>
        <?php echo htmlspecialchars($first) ?>
    </span>
    <span class="flex-1 content-repeat-[.] text-gray ml-1"></span>
    <?php if ($second !== '') { ?>
        <span class="ml-1">
            <?php echo htmlspecialchars($second) ?>
        </span>
    <?php } ?>
</div>
