@extends('layouts.adIndex')
@section('title')
	评论列表
@stop
@section('crumbs')
	评论列表
@stop
@section('search')
{{ Form::open(array('url' => '/admin/getCommentList','method'=>'post')) }}
	{{Form::token();}}
	{{Form::label('uid','评论人id:');}}{{Form::text('uid',$searcharr['uid'],array('class'=>'form-control' ,'style'=>'width:100px;display:inline'))}}
    {{Form::label('nick','评论人昵称:')}}{{Form::text('nick',$searcharr['nick'],array('class'=>'form-control','style'=>'width:150px;display:inline'))}}
    
    {{Form::label('toid','被评论人id:');}}{{Form::text('toid',$searcharr['toid'],array('class'=>'form-control' ,'style'=>'width:100px;display:inline'))}}
    {{Form::label('tonick','被评论人昵称:')}}{{Form::text('tonick',$searcharr['tonick'],array('class'=>'form-control','style'=>'width:150px;display:inline'))}}
    {{Form::submit('搜索',array('class'=>"btn btn-default"));}}
{{ Form::close() }}

@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>评论id</th>
			<th>作品id</th>
			<th>作品主人id<i class="icon-search"></i></th>
			<th>作品主人昵称</th>
			<th>评论人id</th>
			<th>评论人昵称</th>
			<th>被评论人id</th>
			<th>被评论人昵称</th>
			<th>作品名称</th>
			<th>评论内容</th>
			<th>作品添加时间</th>
			<th>评论添加时间</th>
			<th>修改</th>
			<th>删除/恢复</th>
		</tr>
		@foreach ($commentlist as $item)
		<tr>
			<td>{{$item['commentid']}}</td>
			<td>{{$item['id']}}</td>
			<td>{{$item['uid']}}</td>
			<td>
				@if(empty($item['nick']))
					未知
				@else
					{{$item['nick']}}
				@endif
			</td>
			<td>{{$item['fromid']}}</td>
			<td>
				@if(empty($item['fromnick']))
					未知
				@else
					{{$item['fromnick']}}
				@endif
			</td>
			<td>
				@if(empty($item['toid']))
					未知
				@else
					{{$item['toid']}}
				@endif
			</td>
			<td>
				@if(empty($item['tonick']))
					未知
				@else
					{{$item['tonick']}}
				@endif
			</td>
			<td>{{$item['name']}}</td>
			<td>{{unserialize($item['content'])}}</td>
			<td>{{date('Y-m-d H:i',$item['opusaddtime'])}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
			<td>
				<button class="modify btn btn-mini btn-danger" type="button" value='{{$item['commentid']}}'>修改</button>
			</td>
			<td>
				@if($item['isdel'] == 0)
					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['commentid']}}|{{$item['id']}}|0'>删除</button>
				@elseif($item['isdel'] == 1) 
					<button class="operator btn btn-mini btn-success" type="button" value='{{$item['commentid']}}|{{$item['id']}}|1'>恢复</button>
				@endif
			</td>
		</tr>
		@endforeach
	</table>
	{{ $rs->appends(array('uid'=>$searcharr['uid'],'nick'=>$searcharr['nick'],'toid'=>$searcharr['toid'],'tonick'=>$searcharr['tonick']))->links();  }}
<script type="text/javascript">
	$(function() {
		$('.operator').each(function(){
			$(this).click(function() {
				var commentIdSign = $(this).val();
				var arr = commentIdSign.split('|');
				var commentId = arr[0];
				var opusId = arr[1];
				var sign = arr[2];
				var token = $('input[name=_token]').val();
				if(token == null){
					alert('数据错误');
					return;
				}
				$.post('/admin/delOrDelComment',{commentId:commentId,opusId:opusId,sign:sign,'_token':token},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			});
		});
	});
</script>
@stop

