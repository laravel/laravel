@extends('layouts.adIndex')
@section('title')
	报名列表
@stop
@section('crumbs')
	报名列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id<i class="icon-search"></i></th>
			<th>UID</th>
            <th>姓名</th>
            <th>昵称</th>
            <th>性别</th>
			<th>工作单位</th>
			<th>毕业院校</th>
			<th>联系电话</th>
			<th>朗诵范文的理由</th>
			<th width="100">提交时间</th>
			<th>删除</th>
		</tr>
		@foreach ($list as $item)
		<tr>
			<td>{{$item['id']}}</td>
            <td>
            <?php
            if(isset($users[$item['uid']])){
				echo $item['uid'];
			}else{
				echo '-';
			}
			?>
            </td>
			<td>{{$item['name']}}</td>
            <td>
            <?php
            if(isset($users[$item['uid']])){
				echo $users[$item['uid']]['nick'];
			}else{
				echo '-';
			}
			?>
            </td>
            <td>
            <?php
            if(isset($users[$item['uid']])){
				echo $users[$item['uid']]['gender']==1?'男':'女';
			}else{
				echo '-';
			}
			?>
            </td>
			<td>{{$item['company']}}</td>
			<td>{{$item['school']}}</td>
			<td>{{$item['tel']}}</td>
			<td>{{$item['reason']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				<button class="operator btn btn-mini btn-success" type="button" value='{{$item['id']}}'>删除</button>
			</td>
		</tr>
		@endforeach
	</table>
	{{$data->links();}}
	<script type="text/javascript">
		$('.operator').each(function() {
			var id = $(this).val();
			$(this).click(function() {
				$(this).parent().parent().remove();
				$.post('/admin/delSignUp',{id:id},function(data) {
					if('error'==data) {
						alert("删除失败");
					}
				});
			});
		})
	</script>
@stop

