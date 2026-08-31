<?php /* List data-table values, i.e: $_SERVER, $_GET, .... */ ?>
<div class="details">
  <h2 class="details-heading">Environment &amp; details:</h2>

  <div class="data-table-container" id="data-tables">
    <?php foreach ($tables as $label => $data): ?>
      <div class="data-table" id="sg-<?php echo $tpl->escape($tpl->slug($label)) ?>">
        <?php if (!empty($data)): ?>
            <label><?php echo $tpl->escape($label) ?></label>
            <table class="data-table">
              <thead>
                <tr>
                  <td class="data-table-k">Key</td>
                  <td class="data-table-v">Value</td>
                </tr>
              </thead>
            <?php foreach ($data as $k => $value): ?>
              <tr>
                <td><?php echo $tpl->escape($k) ?></td>
                <td><?php echo $tpl->dump($value) ?></td>
              </tr>
            <?php endforeach ?>
            </table>
        <?php else: ?>
            <label class="empty"><?php echo $tpl->escape($label) ?></label>
            <span class="empty">empty</span>
        <?php endif ?>
      </div>
    <?php endforeach ?>
  </div>

  <?php /* List registered handlers, in order of first to last registered */ ?>
  <div class="data-table-container" id="handlers">
    <label>Registered Handlers</label>
    <?php foreach ($handlers as $i => $h): ?>
      <div class="handler <?php echo ($h === $handler) ? 'active' : ''?>">
        <?php echo $i ?>. <?php echo $tpl->escape(get_class($h)) ?>
      </div>
    <?php endforeach ?>
  </div>

</div>
