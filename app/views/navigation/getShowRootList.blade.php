@extends('layouts.adIndex')
@section('title')
	导航列表|添加伴奏分类
@stop
@section('search')
	<form action="{{ url('/admin/addShowRoot') }}"
	        method="post"
	        enctype="multipart/form-data"
			>
		<table class="table table-hover table-bordered ">
			<tr>
				<td width="150px">上传图片</td>
				<td><input type="file" name="showbootpic" /></td>
			</tr>
			<tr>
				<td>用户id</td>
				<td><input type="input" name="flag" /></td>
			</tr>
			<tr>
				<td>比赛id/活动id</td>
				<td><input type="input" name="com_flag" /><td>
			</tr>
			<tr>
				<td>类型</td>
				<td>
                <input type="radio" name="type" class="ck_id"  value="3">诵读联合会
                <input type="radio" name="type" class="ck_id"  value="5">诗经奖
                <input type="radio" name="type" class="ck_id"  value="6">比赛
                <input type="radio" name="type" class="ck_id"  value="7">外链
                <input type="radio" name="type" class="ck_id"  value="8">静态图片，不做任何操作
                <input type="radio" name="type" class="ck_id"  value="9">活动观众报名
                 <input type="radio" name="type" class="ck_id"  value="11">班级活动报名
                <td>
			</tr>
            <tr id="link_tr" style="display:none;">
            	<td>外链地址</td>
				<td>
                <input type="text" name="html_link" style="width:300px;" />
                </td>
            </tr>
			<tr text-align="center">
				<td  colspan="2"><input class="operator btn btn-mini btn-danger" type="submit"  value='添加'></td>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	导航列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>图片id</th>
			<th>图片</th>
			<th>用户id</th>
			<th>删除</th>
		</tr>
		@foreach ($showrootlist as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td><img style="width:50px;height:50px" src='{{$item['url']}}' /></td>
			<td>{{$item['flag']}}</td>
			<td>
				<button class="myimage btn btn-mini btn-danger" type="button"  value='{{$item['id']}}'>删除</button>
			</td>
		</tr>
		@endforeach
	</table>
<script type="text/javascript">
	$(function() {
		$(".ck_id").click(function(){
			var _id=$(this).val();
			if(_id==7){
				$("#link_tr").show();
			}else{
				$("#link_tr").hide();
			}
		});
		$('.myimage').each(function(){
			$(this).click(function() {
				var myimageid = $(this).val();
				$.post('/admin/delShowList',{myimageid:myimageid},function(data) {
					if('error' == data) {
						alert('操作失败');
						return;
					}
				});
				$(this).parent().parent().hide();
			});
		});
	});
</script>
@stop

