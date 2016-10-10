@extends('layouts.adIndex')
@section('title')
	用户列表
@stop

<form action="{{ url('shangchuan') }}"
        method="post"
        enctype="multipart/form-data"
>
        <input type="file" name="book" />
        <input type="submit" value="Send" />
</form>

