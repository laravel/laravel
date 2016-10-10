@extends('layouts.adIndex')
@section('title')
	添加商品分类
@stop
@section('crumbs')
	添加商品分类
@stop
@section('search')
@stop
@section('content')
{{ Form::open(array('url' => '/admin/addGoodCategory')) }}
{{Form::label('name', '分类名称',array('class'=>'awesome'));}}
{{ Form::text('name','',['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
{{Form::label('sort', '分类顺序',array('class'=>'awesome','style'=>"margin-left:20px"));}}
{{ Form::text('sort','',['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
{{Form::submit('添加',['class'=>"search btn btn-mini btn-success",'style'=>'margin-left:5px'])}}
{{ Form::close() }}
<br/><br/>
<table class="table table-hover table-bordered ">
	<tr>
		<th>id</th>
		<th>分类名称</th>
		<th>排序</th>
		<th>添加时间</th>
		<th>状态</th>
	</tr>
	@if(!empty($rs))
		@foreach($rs as $k=>$v)
		<tr>
			<td>{{$v['id']}}</td>
			<td><input class="operator form-control" type="text" value='{{$v["name"]}}'  data-id='{{$v["id"]}}' data-flag="1"/></td>
			<td><input class="operator form-control" type="text" value='{{$v["sort"]}}'  data-id='{{$v["id"]}}' data-flag="2"/></td>
			<td>{{date('Y-m-d',$v['addtime'])}}</td>
			<td>
				@if($v['status'] == 0)
					正常
				@else
					删除
				@endif
			</td>
		</tr>
		@endforeach
	@endif
</table>
<script type="text/javascript">
	$(".operator").bind('focusout',function(data){
		var val = $(this).val();
		var id = $(this).attr('data-id');
		var data_flag = $(this).attr('data-flag');
		$.post('/admin/modGoodCategory',{id:id,val:val,data_flag:data_flag},function(data){
			if(data=='error')
			{
				alert("修改错误，请重试");
				return;
			}
			else
			{
				alert('修改成功');
				location.reload();
			}
		});
	});
</script>
@stop