@extends('layouts.adIndex')
@section('title')
	错误页面
@stop
@section('crumbs')
	错误页面
@stop
@section('search')
@stop
@section('content')
	
	<div class="jumbotron">
		<div class="alert alert-danger" role="alert">{{Session::get('message')}}</div>
	  	<p>点击下面链接返回上一页</p>
	  	<p><a class="btn btn-primary btn-lg" href="javascript:history.go(-1)" role="button">点击返回上一页</a></p>
	</div>
@stop



