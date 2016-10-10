<?php
/**
* Template file for Whoops's pretty error output.
* Check the $v global variable (stdClass) for what's available
* to work with.
* @var stdClass $v
* @var callable $e
* @var callable $slug
*/
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $e($v->title) ?></title>

    <style><?php echo $v->pageStyle ?></style>
  </head>
  <body>
    <div class="container">

      <div class="stack-container">

        <div class="frames-container cf <?php echo (!$v->hasFrames ? 'empty' : '') ?>">

          <?php /* List file names & line numbers for all stack frames;
                   clicking these links/buttons will display the code view
                   for that particular frame */ ?>
          <?php foreach($v->frames as $i => $frame): ?>
            <?php /** @var \Whoops\Exception\Frame $frame */ ?>
            <div class="frame <?php echo ($i == 0 ? 'active' : '') ?>" id="frame-line-<?php echo $i ?>">
                <div class="frame-method-info">
                  <span class="frame-index"><?php echo (count($v->frames) - $i - 1) ?>.</span>
                  <span class="frame-class"><?php echo $e($frame->getClass() ?: '') ?></span>
                  <span class="frame-function"><?php echo $e($frame->getFunction() ?: '') ?></span>
                </div>

              <span class="frame-file">
                <?php echo ($frame->getFile(true) ?: '<#unknown>') ?><!--
             --><span class="frame-line"><?php echo (int) $frame->getLine() ?></span>
              </span>
            </div>
          <?php endforeach ?>

        </div>

        <div class="details-container cf">

          <header>
            <div class="exception">
              <h3 class="exc-title">
                <?php foreach($v->name as $i => $nameSection): ?>
                  <?php if($i == count($v->name) - 1): ?>
                    <span class="exc-title-primary"><?php echo $e($nameSection) ?></span>
                  <?php else: ?>
                    <?php echo $e($nameSection) . ' \\' ?>
                  <?php endif ?>
                <?php endforeach ?>
              </h3>
              <p class="exc-message">
                <?php echo $e($v->message) ?>
              </p>
            </div>
          </header>

          <?php /* Display a code block for all frames in the stack.
                 * @todo: This should PROBABLY be done on-demand, lest
                 * we get 200 frames to process. */ ?>
          <div class="frame-code-container <?php echo (!$v->hasFrames ? 'empty' : '') ?>">
            <?php foreach($v->frames as $i => $frame): ?>
              <?php /** @var \Whoops\Exception\Frame $frame */ ?>
              <?php $line = $frame->getLine(); ?>
                <div class="frame-code <?php echo ($i == 0 ) ? 'active' : '' ?>" id="frame-code-<?php echo $i ?>">
                  <div class="frame-file">
                    <?php $filePath = $frame->getFile(); ?>
                    <?php if($filePath && $editorHref = $v->handler->getEditorHref($filePath, (int) $line)): ?>
                      Open:
                      <a href="<?php echo $editorHref ?>" class="editor-link">
                        <strong><?php echo $e($filePath ?: '<#unknown>') ?></strong>
                      </a>
                    <?php else: ?>
                      <strong><?php echo $e($filePath ?: '<#unknown>') ?></strong>
                    <?php endif ?>
                  </div>
                  <?php
                    // Do nothing if there's no line to work off
                    if($line !== null):

                    // the $line is 1-indexed, we nab -1 where needed to account for this
                    $range = $frame->getFileLines($line - 8, 10);
                    $range = array_map(function($line){ return empty($line) ? ' ' : $line;}, $range);
                    $start = key($range) + 1;
                    $code  = join("\n", $range);
                  ?>
                  <pre class="code-block prettyprint linenums:<?php echo $start ?>"><?php echo $e($code) ?></pre>
                  <?php endif ?>

                  <?php
                    // Append comments for this frame */
                    $comments = $frame->getComments();
                  ?>
                  <div class="frame-comments <?php echo empty($comments) ? 'empty' : '' ?>">
                    <?php foreach($comments as $commentNo => $comment): ?>
                      <?php
                        extract($comment)

                        /**
                         * @var string $context
                         * @var string $comment
                         */
                      ?>
                      <div class="frame-comment" id="comment-<?php echo $i . '-' . $commentNo ?>">
                        <span class="frame-comment-context"><?php echo $e($context) ?></span>
                        <?php echo $e($comment, true) ?>
                      </div>
                    <?php endforeach ?>
                  </div>

                </div>
            <?php endforeach ?>
          </div>

          <?php /* List data-table values, i.e: $_SERVER, $_GET, .... */ ?>
          <div class="details">
            <div class="data-table-container" id="data-tables">
              <?php foreach($v->tables as $label => $data): ?>
                <div class="data-table" id="sg-<?php echo $e($slug($label)) ?>">
                  <label><?php echo $e($label) ?></label>
                  <?php if(!empty($data)): ?>
                      <table class="data-table">
                        <thead>
                          <tr>
                            <td class="data-table-k">Key</td>
                            <td class="data-table-v">Value</td>
                          </tr>
                        </thead>
                      <?php foreach($data as $k => $value): ?>
                        <tr>
                          <td><?php echo $e($k) ?></td>
                          <td><?php echo $e(print_r($value, true)) ?></td>
                        </tr>
                      <?php endforeach ?>
                      </table>
                  <?php else: ?>
                    <span class="empty">empty</span>
                  <?php endif ?>
                </div>
              <?php endforeach ?>
            </div>

            <?php /* List registered handlers, in order of first to last registered */ ?>
            <div class="data-table-container" id="handlers">
              <label>Registered Handlers</label>
              <?php foreach($v->handlers as $i => $handler): ?>
                <div class="handler <?php echo ($handler === $v->handler) ? 'active' : ''?>">
                  <?php echo $i ?>. <?php echo $e(get_class($handler)) ?>
                </div>
              <?php endforeach ?>
            </div>

          </div> <!-- .details -->
        </div>

      </div>
    </div>

    <script src="//cdnjs.cloudflare.com/ajax/libs/prettify/r224/prettify.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script>
      $(function() {
        prettyPrint();

        var $frameLines  = $('[id^="frame-line-"]');
        var $activeLine  = $('.frames-container .active');
        var $activeFrame = $('.active[id^="frame-code-"]').show();
        var $container   = $('.details-container');
        var headerHeight = $('header').css('height');

        var highlightCurrentLine = function() {
          // Highlight the active and neighboring lines for this frame:
          var activeLineNumber = +($activeLine.find('.frame-line').text());
          var $lines           = $activeFrame.find('.linenums li');
          var firstLine        = +($lines.first().val());

          $($lines[activeLineNumber - firstLine - 1]).addClass('current');
          $($lines[activeLineNumber - firstLine]).addClass('current active');
          $($lines[activeLineNumber - firstLine + 1]).addClass('current');
        };

        // Highlight the active for the first frame:
        highlightCurrentLine();

        $frameLines.click(function() {
          var $this  = $(this);
          var id     = /frame\-line\-([\d]*)/.exec($this.attr('id'))[1];
          var $codeFrame = $('#frame-code-' + id);

          if($codeFrame) {
            $activeLine.removeClass('active');
            $activeFrame.removeClass('active');

            $this.addClass('active');
            $codeFrame.addClass('active');

            $activeLine  = $this;
            $activeFrame = $codeFrame;

            highlightCurrentLine();

            $container.animate({ scrollTop: headerHeight }, "fast");
          }
        });
      });
    </script>
  </body>
</html>
