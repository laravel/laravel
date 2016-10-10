@extends('layouts.adIndex')
@section('title')
	用户和范读导师关联
@stop
@section('search')
	{{ Form::open(array('url' => '/admin/addRelUser')) }}
	{{Form::label('uid', '用户ID',array('class'=>'awesome'));}}
	{{ Form::text('uid','',['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
	{{Form::label('reader_id', '导师id',array('class'=>'awesome','style'=>"margin-left:20px"));}}
	{{ Form::text('reader_id','',['class'=>'form-control','style'=>'width:200px;display:inline;margin-left:10px'])}}
	{{Form::submit('添加',['class'=>"search btn btn-mini btn-success",'style'=>'margin-left:5px'])}}
	{{ Form::close() }}
@stop
@section('crumbs')
@stop
@section('content')
<style type="text/css">
.sex{ background-color:transparent; border:0px; width:50px; }
.sex2{ background-color:#FFF; border:1px solid #CCC; width:50px; }
</style>
	<table class="table table-hover table-bordered ">
		<tr>
			<th>用户id<i class="icon-search"></i></th>
            			<th>用户昵称</th>
            			<th>用户头像</th>
			<th>导师id</th>
			<th>导师姓名</th>
			<th>关联时间</th>
			<th>删除关联</th>
		</tr>
		@foreach($list as $k=>$v)
		<tr>
			<td>{{$v['uid']}}</td>
			<td>{{$v['nick']}}</td>
			<td>{{$v['sportrait']}}</td>
			<td>{{$v['reader_id']}}</td>
			<td>{{$v['name']}}</td>
			<td>{{date('Y-m-d')}}</td>
			<td>
				<button class="operator btn btn-mini btn-danger" type="button"  value="{{$v['id']}}">删除关联</button>	
			</td>
		</tr>
		@endforeach
	</table>
<script type="text/javascript">
	$('.operator').bind('click',function(data){
		id = $(this).val();
		if(id == null || id == undefined){
			alert('删除失败');
			return;
		}
		if(confirm('确定删除关联吗')){
			$.ajax({
				method:"POST",
				url:'/admin/delRelUser',
				data:{id:id}
			}).done(function(data){
				if(data == -1){
					alert('删除失败');
					return;
				}else{
					alert('删除成功');
					window.location.reload();
				}
			});
		}
		
	});
</script>
@stop

