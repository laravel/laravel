@extends('layouts.adIndex')
@section('title')
	评委列表
@stop
@section('search')
	@include('jury.jurysearch')
@stop
@section('crumbs')
	评委列表
@stop

@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>评委id</th>
			<th>用户id</th>
			<th>真实姓名<i class="icon-search"></i></th>
			<th width="100px">昵称</th>
			<th>评委级别</th>
			<th>评委头像</th>
			<th>用户头像</th>
			<th>排序</th>
			<th>所属赛事</th>
			<th>删除/恢复</th>

		</tr>
		@if(!empty($jurylist))
			@foreach ($jurylist as $item)
			<tr>
				<td>{{$item['id']}}</td>
				<td>{{$item['uid']}}</td>
				<td>{{$item['name']}}</td>
				<td style="width:100px">
					@if(!empty($item['userInfo']['nick']))
						{{$item['userInfo']['nick']}}
					@endif
				</td>
				<td>
					@if($item['level'] == 1)
						总决赛评委
					@else
						分赛区评委
					@endif
				</td>
				<td><img src='{{$item['thumb']}}' style="width:100px;height:100px" /></td>
				<td>
					@if(!empty($item['userInfo']['sportrait']))
						<img src='{{$item['userInfo']['sportrait']}}' style="width:100px;height:100px" />
					@endif
				</td>
				<td>
					<input class="modifyJury" type="text" name="sort" id="sort" value="{{$item['sort']}}" old-sort="{{$item['sort']}}" data-id="{{$item['id']}}" data-type="{{$item['type']}}" data-level="{{$item['level']}}">
				</td>
				<td>{{$all_competition[$item['type']]}}</td>
				@if($item['status'] == 1)
					<td><button class="operator btn btn-mini btn-success" data-status = {{$item['status']}} type="button"  value='{{$item['id']}}'>恢复</button></td>
				@elseif($item['status'] == 2)
					<td><button class="operator btn btn-mini btn-danger" data-status = {{$item['status']}} type="button"  value='{{$item['id']}}'>删除</button></td>
				@endif
			</tr>
			@endforeach
		@endif
	</table>
		@if(!empty($links))
			{{ $links->links()  }}
		@endif
<script type="text/javascript">
	$(function() {
		$('.operator').each(function(){
			$(this).click(function() {
				var id = $(this).val();
				var data_status = $(this).attr('data-status');
				$.post('/admin/delJury',{id:id,data_status:data_status},function(data) {
					if(-1 == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});

		$('.modifyJury').each(function(){
			$(this).focusout(function(){
				var id = $(this).attr('data-id');
				var sort = $(this).val();
				var old_sort = $(this).attr('old-sort');
				var type = $(this).attr('data-type');
				var level = $(this).attr('data-level');
				if(old_sort == sort){
					return;
				}
				$.post('/admin/modifyJurySort',{id:id,sort:sort,old_sort:old_sort,type:type,level:level},function(data){
					if(data == 1){
						alert('修改成功');
						window.location.reload();
						return;
					}else{
						alert('修改失败');
					}
				});
			});
		});
	});
</script>
@stop


