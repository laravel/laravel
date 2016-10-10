@extends('layouts.adIndex')
@section('title')
	添加结束比赛商品
@stop
@section('crumbs')
	添加结束比赛商品
@stop
@section('search')
@stop

@section('content')
{{Form::open(array('url'=>"/admin/doUpdateFinishCompGoods",'method'=>'post'));}}
	<table class="table table-hover table-bordered ">
		<tr>
			<th>{{Form::label('id','商品id:')}}</th>
			<td>
				{{Form::text('id',$good_info['id'],array('class'=>'form-control' ));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('name','商品名称:')}}</th>
			<td>
				{{Form::text('name',$good_info['name'],array('class'=>'form-control' ));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('description','描述信息:')}}</th>
			<td>{{Form::textarea('description',$good_info["description"],array('class'=>'form-control'))}}</td>
		</tr>
		<tr>
			<th>{{Form::label('price','价格:')}}</th>
			<td>
				{{Form::text('price',$good_info["price"],array('class'=>'form-control'));}}
			</td>
		</tr>
		<tr>
			<th>{{Form::label('type','商品时效')}}</th>
			<td>{{Form::select('type',[1=>'次'],1,['class'=>'form-control']);}}</td>
		</tr>
		<tr>
			<th>{{Form::label('competition_id','比赛id')}}</th>
			<td>{{Form::select('competition_id',$finish_comp,$good_info["competition_id"],array('class'=>'form-control'));}}</td>
		</tr>
		<tr>
			<th>光盘价格</th>
			<td>{{Form::text('cd_price',$good_info["cd_price"],['class'=>'form-control'])}}</td>
		</tr>
		<tr>
			<th>光盘名称</th>
			<td>{{Form::text('cd_name',$good_info["cd_name"],['class'=>'form-control'])}}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('更新',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
@stop

