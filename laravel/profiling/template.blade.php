<!-- ANBU - LARAVEL PROFILER -->
<style type="text/css">{{ file_get_contents(path('sys').'profiling/profiler.css') }}</style>
<div class="anbu">
	<div class="anbu-window">
		<div class="anbu-content-area">
			<div class="anbu-tab-pane anbu-table anbu-log">
				@if (count($logs) > 0)
					<table>
						<tr>
							<th>Type</th>
							<th>Message</th>
						</tr>
						@foreach ($logs as $log)
							<tr>
								<td class="anbu-table-first">
									{{ $log[0] }}
								</td>
								<td>
									{{ print_r($log[1]) }}
								</td>
						@endforeach
						</tr>
					</table>
				@else
					<span class="anbu-empty">There are no log entries.</span>				
				@endif
			</div>

			<div class="anbu-tab-pane anbu-table anbu-sql">
				@if (count($queries) > 0)
					<table>
						<tr>
							<th>Time</th>
							<th>Query</th>
						</tr>
						@foreach ($queries as $query)
							<tr>
								<td class="anbu-table-first">
									{{ $query[1] }}ms
								</td>
								<td>
									<pre>{{ print_r($query[0]) }}</pre>
								</td>
							</tr>
						@endforeach
					</table>
				@else
					<span class="anbu-empty">There have been no SQL queries executed.</span>
				@endif
			</div>
		</div>
	</div>

	<ul id="anbu-open-tabs" class="anbu-tabs">
		<li><a data-anbu-tab="anbu-log" class="anbu-tab" href="#">Log <span class="anbu-count">{{ count($logs) }}</span></a></li>
		<li><a data-anbu-tab="anbu-sql" class="anbu-tab" href="#">SQL <span class="anbu-count">{{ count($queries) }}</span></a></li>
		<li class="anbu-tab-right"><a id="anbu-hide" href="#">&#8614;</a></li>
		<li class="anbu-tab-right"><a id="anbu-close" href="#">&times;</a></li>
		<li class="anbu-tab-right"><a id="anbu-zoom" href="#">&#8645;</a></li>
	</ul>

	<ul id="anbu-closed-tabs" class="anbu-tabs">
		<li><a id="anbu-show" href="#">&#8612;</a></li>
	</ul>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script>{{ file_get_contents(path('sys').'profiling/profiler.js') }}</script>
<!-- /ANBU - LARAVEL PROFILER -->