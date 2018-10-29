@extends('<!MODEL!>.layout')

@section('title')
	Create
@endsection

@section('body')
	<a href="/<!MODEL!>"><h1>Index</h1></a>
	<hr>

  <form action="/<!MODEL!>/create" method="post">
  	@csrf
    @foreach($desc as $ar)

    	@if($ar['Field'] == 'id')
    		@php continue; @endphp

    	@elseif(strpos($ar['Type'], 'int') !== false)
    		<label for="{{$ar['Field']}}">{{ucfirst($ar['Field'])}}:</label>
			<input type="number" class="form-control" name="{{$ar['Field']}}">
    	
    	@elseif(strpos($ar['Type'], 'bit') !== false)
          	<label for="{{$ar['Field']}}">{{ucfirst($ar['Field'])}}:</label>
			<input type="checkbox" class="form-control" name="{{$ar['Field']}}">
        
        @else
      		<label for="{{$ar['Field']}}">{{ucfirst($ar['Field'])}}:</label>
			<input type="text" class="form-control" name="{{$ar['Field']}}">
        @endif

        <br>
    @endforeach

    <hr>
    <button type="submit" class="btn btn-success">Create</button>
</form>

<br>
@endsection