<!-- ANBU - LARAVEL PROFILER -->
<style type="text/css"><?php echo $anbu_css ?></style>
<div class="anbu">
	<div class="anbu-window">
		<div class="anbu-content-area">
			<?php if ($anbu_config['tab_logs']) : ?>
			<div class="anbu-tab-pane anbu-table anbu-log">
				<?php if (count($anbu_logs)) : ?>
				<table>
					<tr>
						<th>Type</th>
						<th>Message</th>
					</tr>
					<?php foreach($anbu_logs as $l) : ?>
					<tr>
						<td class="anbu-table-first"><?php echo $l[0]; ?></td>
						<td><?php print_r($l[1]); ?></td>
					<?php endforeach; ?>
					</tr>
				</table>
				<?php else : ?>
					<span class="anbu-empty">There are no log entries.</span>
				<?php endif; ?>
			</div>
			<?php endif; ?>
			<?php if ($anbu_config['tab_queries']) : ?>
			<div class="anbu-tab-pane anbu-table anbu-sql">
				<?php if (count($anbu_queries)) : ?>
				<table>
					<tr>
						<th>Time</th>
						<th>Query</th>
					</tr>
					<?php foreach($anbu_queries as $s) : ?>
					<tr>
						<td class="anbu-table-first"><?php echo $s[1]; ?>ms</td>
						<td><pre><?php print_r($s[0]); ?></pre></td>
					<?php endforeach; ?>
					</tr>
				</table>
				<?php else : ?>
					<span class="anbu-empty">There have been no SQL queries executed.</span>
				<?php endif; ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
	<ul id="anbu-open-tabs" class="anbu-tabs">
		<?php if ($anbu_config['tab_logs']) : ?><li><a data-anbu-tab="anbu-log" class="anbu-tab" href="#">Log <span class="anbu-count"><?php echo count($anbu_logs); ?></span></a></li><?php endif; ?>
		<?php if ($anbu_config['tab_queries']) : ?><li><a data-anbu-tab="anbu-sql" class="anbu-tab" href="#">SQL <span class="anbu-count"><?php echo count($anbu_queries); ?></span></a></li><?php endif; ?>
		<li class="anbu-tab-right"><a id="anbu-hide" href="#">&#8614;</a></li>
		<li class="anbu-tab-right"><a id="anbu-close" href="#">&times;</a></li>
		<li class="anbu-tab-right"><a id="anbu-zoom" href="#">&#8645;</a></li>
	</ul>

	<ul id="anbu-closed-tabs" class="anbu-tabs">
		<li><a id="anbu-show" href="#">&#8612;</a></li>
	</ul>
</div>
<?php if ($anbu_config['include_jquery']) : ?><script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script><?php endif; ?>
<script><?php echo $anbu_js ?></script>
<!-- /ANBU - LARAVEL PROFILER -->
