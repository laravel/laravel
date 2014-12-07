@extends('app', ['down'=>true])

@section('content')
<div id="down">
    <div class="jumbotron">
        <div class="container">
            <h1 class="jumbotron__header">Down for Maintenance!</h1>

            <p class="jumbotron__body">
                Sorry for the inconveniance this may cause. We should be back in less than 15 minutes!
            </p>

            <p class="jumbotron__body"><b>
                If you're seeing this for more than fifteen minutes, here's what you can do:
            </b></p>
			<p class="jumbotron__body">
				<span id="refresh" onclick='location.reload()'>Refresh your page.</span>
			</p>
            <p class="jumbotron__body">
            	Email us at <a href="{{ Config::get('project.company.email') }}">
                {{ Config::get('project.company.email') }}</a>
            </p>
        </div>
    </div>



               
</div>
@stop
