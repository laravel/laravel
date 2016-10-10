@extends('layouts.adIndex')
@section('title')
	最火作品榜
@stop
@section('search')
	<form action="/admin/addRecommendOpus" method='post'>
		<table>
			<tr>
				<td>作品名称</td>
				<td style="width:300px"><input id = 'opusname' name="opusname" type='text' class="form-control" /></td>
				<td>主人昵称</td>
				<td style="width:300px"><input id = 'nick' name="nick" type='text' class="form-control" /></td>
				<td colspan=2>
					<button class="search btn btn-mini btn-success" type="button"  value=''>查询</button>
				</td>
			</tr>
			<tr>
				<td>查询结果</td>
				<td style="width:300px" colspan=4>
					<select id = 'lastdata' name='lastdata' class="form-control">
					</select>
				</td>
			<tr>
			<tr>
				<td>作品排序</td>
				<td><input type="text" id = 'opussort' name='opussort' class='form-control' value='' /></td>
				<td colspan=4>
					<input type='submit' value="提交"/>
				</td>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	最火作品榜
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>作品排行</th>
			<th>作品id<i class="icon-search"></i></th>
			<th>用户id</th>
			<th>用户昵称</th>
            <th>性别</th>
			<th>作品名称</th>
			<th>作品赞数</th>
			<th>作品收听数</th>
			<th>作品转发数</th>
			<th>作品添加时间</th>
			<th>删除排序</th>
		</tr>
		@foreach ($recommendopusboard as $item)
		<tr>
			<td><input type = 'text' name='recommendopus' class='recommendopus' value='{{$item['recommendopus']}}'/></td>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>{{$item['nick']}}</td>
            <td>
            @if($item['gender']==1)
                男
            @else
                女
            @endif
            </td>
			<td>{{$item['name']}}</td>
			<td>{{$item['praisenum']}}</td>
			<td>{{$item['lnum']}}</td>
			<td>{{$item['repostnum']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|{{$item['recommendopus']}}'>删除排序</button>
			</td>
		</tr>
		@endforeach
	</table>
	{{ $recommendopusboard->links()  }}
<script type="text/javascript">
	$('.search').click(
		function() {
			var opusname = $('#opusname').val();
			var nick = $('#nick').val();
			if(opusname.length<=0 || nick.length <=0) {
				alert('作品名或者用户昵称不能为空');
				return;
			}

			$.post('/admin/searchOpus',{opusname:opusname,nick:nick},function(data) {
				if('error' == data) {
					alert('请输入正确的作品名或用户昵称');
					return;
				} else {
					//清空原来的输入
					$('#lastdata').empty();
					$('#lastdata').append(data);
					// location.reload();
				}
			});
		}
	);

	$(function() {
		$('.recommendopus').each(function(){
			$(this).focusout(function() {
				var  recommendopus= $(this).val();
				var opusid = $(this).parent().next().html();
				$.post('/admin/modifyRecommendOpus',{opusid:opusid,recommendopus:recommendopus},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});

	$(function() {
		$('.operator').each(function() {
			$(this).click(function() {
				//当前元素下面所有元素的值-1
				$(this).parent().parent().remove();
				var tmpStr = $(this).val();
				var tmpArr = tmpStr.split('|');
				var opusid = tmpArr[0];
				var recommendopus = tmpArr[1];
				$.post('/admin/delRecommendOpus',{opusid:opusid,recommendopus:recommendopus},function(data) {
					if('error' == data) {
						alert('删除失败');
					} else {
						location.reload();
					}
				});
			});
		});
	});
</script>
@stop

