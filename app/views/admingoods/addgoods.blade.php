@extends('layouts.adIndex')
@section('title')
	添加商品
@stop
@section('crumbs')
	添加商品
@stop
@section('search')
@stop

@section('content')
{{Form::open(array('url'=>"/admin/addGoods",'method'=>'post'));}}
	<table class="table table-hover table-bordered ">
		<tr>
			<th>{{Form::label('name','商品名称:')}}</th>
			<td>
				{{Form::text('name','',array('class'=>'form-control' ));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('description','描述信息:')}}</th>
			<td>{{Form::textarea('description','',array('class'=>'form-control'))}}</td>
		</tr>
		<tr>
			<th>{{Form::label('price','价格:')}}</th>
			<td>
				{{Form::text('price',0,array('class'=>'form-control'));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('discount_price','折扣价格')}}</th>
			<td>{{Form::text('discount_price',0,array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('type','商品时效')}}</th>
			<td>{{Form::select('type',$type);}}</td>
		</tr>
		<tr>
			<th>{{Form::label('flag','商品类型')}}</th>
			<td>{{Form::select('flag',$goods_category)}}</td>
		</tr>
		<tr>
			<th>{{Form::label('competition_id','比赛id')}}</th>
			<td>{{Form::text('competition_id',0,array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('start_time','折扣开始时间')}}</th>
			<td>{{Form::text('start_time',date('Y-m-d H:i:s',time()),array('class'=>'form-control' ));}}</td>
		</tr>
		<tr>
			<th>{{Form::label('end_time','折扣结束时间')}}</th>
			<td>{{Form::text('end_time',date('Y-m-d H:i:s',time()),array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('添加',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
@stop

