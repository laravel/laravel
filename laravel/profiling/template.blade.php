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
									{{ $log[1] }}
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
									{{ number_format($query[1], 2) }}ms
								</td>
								<td>
									<pre>{{ $query[0] }}</pre>
								</td>
							</tr>
						@endforeach
					</table>
				@else
					<span class="anbu-empty">There have been no SQL queries executed.</span>
				@endif
			</div>

			<div class="anbu-tab-pane anbu-table anbu-checkpoints">
				@if (count($timers) > 0)
						<table>
							<tr>
								<th>Name</th>
								<th>Running Time (ms)</th>
								<th>Difference</th>
							</tr>
							@foreach ($timers as $name => $timer)
							<tr>
								<td class="anbu-table-first">
									{{ $name }}
								</td>
								<td><pre>{{ $timer['running_time'] }}ms (time from start to render)</pre></td>
								<td>&nbsp;</td>
							</tr>

							@if (isset($timer['ticks']))
								@foreach( $timer['ticks'] as $tick)
								<tr>
									<td>
										<pre>Tick</pre>
									</td>
									<td>
										<pre>{{ $tick['time'] }}ms</pre>
									</td>
									<td>
										<pre>+ {{ $tick['diff'] }}ms</pre>
									</td>
								</tr>
								@endforeach
							@else
								<tr>
									<td><pre>Running Time</pre></td>
									<td><pre>{{ $timer['time'] }}ms</pre></td>
									<td>&nbsp;</td>
								</tr>
							@endif
							
							@endforeach
						</table>			
				@else
					<span class="anbu-empty">There have been no checkpoints set.</span>
				@endif
			</div>
		</div>
	</div>

	<ul id="anbu-open-tabs" class="anbu-tabs">
		<li><a data-anbu-tab="anbu-log" class="anbu-tab" href="#">Log <span class="anbu-count">{{ count($logs) }}</span></a></li>
		<li>
			<a data-anbu-tab="anbu-sql" class="anbu-tab" href="#">SQL 
				<span class="anbu-count">{{ count($queries) }}</span>
				@if (count($queries))
				<span class="anbu-count">{{ number_format(array_sum(array_pluck($queries, '1')), 2) }}ms</span>
				@endif
			</a>
		</li>
		<li><a class="anbu-tab" data-anbu-tab="anbu-checkpoints">Time <span class="anbu-count">{{ $time }}ms</span></a></li>
		<li><a class="anbu-tab">Memory <span class="anbu-count">{{ $memory }} ({{ $memory_peak }})</span></a></li>
		<li class="anbu-tab-right"><a id="anbu-hide" href="#">&#8614;</a></li>
		<li class="anbu-tab-right"><a id="anbu-close" href="#">&times;</a></li>
		<li class="anbu-tab-right"><a id="anbu-zoom" href="#">&#8645;</a></li>
	</ul>

	<ul id="anbu-closed-tabs" class="anbu-tabs">
		<li><a id="anbu-show" href="#">&#8612;</a></li>
	</ul>
</div>

<script src='//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
<script>{{ file_get_contents(path('sys').'profiling/profiler.js') }}</script>
<!-- /ANBU - LARAVEL PROFILER -->
