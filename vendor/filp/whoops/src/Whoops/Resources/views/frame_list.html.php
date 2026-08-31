<?php /* List file names & line numbers for all stack frames;
         clicking these links/buttons will display the code view
         for that particular frame */ ?>
<?php foreach ($frames as $i => $frame): ?>
  <div class="frame <?php echo ($i == 0 ? 'active' : '') ?> <?php echo ($frame->isApplication() ? 'frame-application' : '') ?>" id="frame-line-<?php echo $i ?>">
      <span class="frame-index"><?php echo (count($frames) - $i - 1) ?></span>
      <div class="frame-method-info">
        <span class="frame-class"><?php echo $tpl->breakOnDelimiter('\\', $tpl->escape($frame->getClass() ?: '')) ?></span>
        <span class="frame-function"><?php echo $tpl->breakOnDelimiter('\\', $tpl->escape($frame->getFunction() ?: '')) ?></span>
      </div>

    <div class="frame-file">
        <?php echo $frame->getFile() ? $tpl->breakOnDelimiter('/', $tpl->shorten($tpl->escape($frame->getFile()))) : '<#unknown>' ?><!--
   --><span class="frame-line">:<?php echo (int) $frame->getLine() ?></span>
    </div>
  </div>
<?php endforeach;
