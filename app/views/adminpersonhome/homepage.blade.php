@extends('layouts.adIndex')
@section('title')
	个人主页列表管理
@stop
@section('crumbs')
	个人主页列表管理
@stop
@section('search')
	@include('adminpersonhome.search')
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>名称</th>
			<th>图标</th>
			<th>排序</th>
			<th>添加时间</th>
			<th>分类</th>
			<th>修改分栏</th>
			<th>操作</th>
		</tr>
		@if(!empty($last_list))
			@foreach($last_list as $key=>$list)
				<tr>
					 <td colspan="8" style="background:#E0E0E0">
					 	主页分栏{{$key}}
					 </td>
				</tr>
				@foreach($list as $k=>$v)
					<tr>
						<td>{{$v['id']}}</td>
						<td><input class="name" type="text" value="{{$v['name']}}" data-id="{{$v['id']}}" old-data="{{$v['name']}}" style="width:150px" /></td>
						<td style="width:100px">
							{{ HTML::image("$v[icon]", '图标', array( 'width' => 100, 'height' => 100,'id'=>'icon'.$v['id'])) }}
							{{ Form::open(['url' => 'admin/updatePersonHomeIcon/'.$v['id'],'method'=>'post','enctype'=>'multipart/form-data','target'=>'submitIform']) }}
							    	{{ Form::file('icon',['class'=>'form-control','style'=>'width:100px;display:inline'])}}
							    	{{ Form::hidden('flag',$v['flag'])}}<br/>
							    	{{ Form::submit('添加',array('class'=>'search btn btn-mini btn-success' ,'style'=>'width:100px;display:inline'));}}
							    	<iframe name="submitIform" style="display:none;"></iframe>
						</td>
						<td>
							<input  class="sort" type="text" value="{{$v['sort']}}" data-id = "{{$v['id']}}"  old-data="{{$v['sort']}}" style="width:100px"/>
						</td>
						<td>{{date('Y-m-d',$v['addtime'])}}</td>
						<td>
							@if($v['flag'])
								他人
							@else
								<span style="color:red">自己</span>
							@endif
						</td>
						<td>
							<span>分栏{{$key}}</span>
							 {{ Form::select('column',$column,0,array('class'=>'form-control' ,'style'=>'width:100px;display:inline','onclick'=>'changeColumn(this)','id'=>$v['id'])); }}

						</td>
						<td>
						@if($v['status'] == 0)
							<button type="button" class="btn btn-danger" datastatus=0 data-id = "{{$v['id']}}" onclick="opetratorPersonHome(this)">禁用</button>
						@else
							<button type="button" class="btn btn-success" datastatus=1 data-id = "{{$v['id']}}" onclick="opetratorPersonHome(this)">启用</button>
						@endif
						</td>
						{{ Form::close() }}
					</tr>
				@endforeach
			@endforeach
		@endif
		
	</table>
<script type="text/javascript">
	function opetratorPersonHome(Object){
		var status = $(Object).attr('datastatus');
		var data_id = $(Object).attr('data-id');
		if(status == ''|| data_id == ''){
			alert('操作失败');
			return;
		}
		if(confirm('确定执行此操作吗')){
			$.post('/admin/opetratorPersonHome',{status:status,data_id:data_id},function(data){
				alert(data);
				window.location.reload();
			});
		}
		
	}

	$('.sort').bind('focusout',function(data){
		var sort = $(this).val();
		var data_id = $(this).attr('data-id');
		var old_sort = $(this).attr('old-data');
		if(sort == old_sort){
			return;
		}
		if(confirm('确定执行此操作吗')){
			$.post('/admin/updateSort',{id:data_id,sort:sort},function(data){
				alert(data);
				window.location.reload();
			});
		}
	});

	$('.name').bind('focusout',function(data){
		var name = $(this).val();
		var old_name = $(this).attr('old-data');
		var id = $(this).attr('data-id');
		if(name == old_name){
			return;
		}
		if(confirm('确定执行此操作吗')){
			$.post('/admin/updateName',{id:id,name:name},function(data){
				alert(data);
				window.location.reload();
			});
		}
	});
	function changeColumn(Object){
		var id = $(Object).attr('id');
		var category = $(Object).find("option:selected").val();
		if(confirm('确定执行此操作吗')){
			$.post('/admin/updateColumn',{id:id,category:category},function(data){
				if(data == 'error'){
					alert('修改错误');
					return;
				}
				window.location.reload();
			});
		}
	}
</script>
@stop

