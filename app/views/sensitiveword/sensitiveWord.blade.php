@extends('layouts.adIndex')
@section('title')
	禁用词语
@stop
@section('search')
	<table>
		<tr>
			<td>关键词语</td>
			<td style="width:300px"><input id = 'keyword' name="keyword" type='text' class="form-control" value=""/></td>
			<td>
				<button class="btn btn-mini btn-success" type="button" id="addWord">插入关键词</button>
			</td>
		</tr>
	</table>
@stop
@section('crumbs')
	禁用词语
@stop
@section('content')
	<button class="btn btn-mini btn-success" type="button" onclick="importWordTree()">导入关键词字典</button>
	<table class="table table-hover table-bordered ">
		<form action="{{ url('/admin/admdoSenWord') }}"
	        method="post"
	        enctype="multipart/form-data"
			>
			<table class="table table-hover table-bordered ">
				<tr style="text-align:center;color:red">
					<td width="100px">禁用词id</td>
					<td>禁用词语</td>
					<td width="50">操作</td>
				</tr>
				@if(!empty($words))
				@foreach($words as $item)
				<tr style="text-align:center">
					<td width="100px">{{$item['id']}}</td>
					<td>{{$item['word']}}</td>
					<td>
						<button class="search btn btn-mini btn-success" type="button" onclick="admDelSenWord(this)" value="{{$item['id']}}">删除</button>
					</td>
				</tr> 
				@endforeach
				@endif
			</table>
		</form>
	</table>
	{{ $words->links()  }}
	<script type="text/javascript">

		function admDelSenWord(btn)
		{
			var wordid = $(btn).val();
			$.post('/admin/admDelSenWord',{wordid:wordid},function(data) {
				if('error' == data) {
 					alert('没有查到相关数据，请重试');
	 				return;
	 			} else {
	 				window.alert("操作成功");
					window.location.reload();
				}
			});
		}
		$('#addWord').on('click',function(){
			var keyWord = $('#keyword').val();
			$.post('/admin/addSensitiveWord',{keyWord:keyWord},function(data){
				if('error'== data)
				{
					alert('关键词存在');
					return;
				}
				else
				{
					window.alert("操作成功");
					window.location.reload();
				}
			});
		});
		

		function importWordTree()
		{
			$.post('/admin/import_word_to_tree',function(data)
			{
				window.alert('导入成功');
			});
		}


	</script>
@stop

