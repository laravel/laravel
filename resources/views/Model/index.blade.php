@extends('<!MODEL!>.layout')

@section('title')
	Index
@endsection

@php
  $names = [];
@endphp

@section('body')
  <div class="col-lg-12 text-center">
    <a style="margin: 1%;" class="btn btn-primary" href="/<!MODEL!>/create">New +</a><hr>
  </div>

	<div class="col-lg-12">
    <table class="table">
      <thead class="thead-light">
        <tr>
          @foreach($desc as $ar)
            @php $names[]=$ar['Field']; @endphp
            <th>{{ ucfirst($ar['Field']) }}</th>
          @endforeach
        </tr>
      </thead>
    <tbody>
      @foreach($objects as $obj)
        <tr>
          <form action="/<!MODEL!>/update" method="post">
            @csrf
            @foreach($names as $index)
              <td>
                @if($index == 'id')
                  <input type="hidden" value="{{$obj[$index]}}" name="{{$index}}">
                  {{$obj[$index]}}
                @else
                  <input class="form-control" type="text" value="{{$obj[$index]}}" name="{{$index}}">
                @endif
              </td>
            @endforeach
            <td>
              <a style="margin:1px;" href="/<!MODEL!>/delete/{{$obj['id']}}" class="btn btn-default glyphicon glyphicon-remove"></a>
              <button style="margin:1px;" type="submit" class="btn btn-danger glyphicon glyphicon-pencil"></buttom>
            </td>
          </form>
        </tr>
      @endforeach
    </tbody>
    </table>
  </div>
@endsection