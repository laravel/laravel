@extends('layouts.adIndex')
@section('title')
	添加赛诗
@stop
@section('crumbs')
	添加赛诗
@stop
@section('content')
<link rel="stylesheet" href="/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="/kindeditor/lang/zh_CN.js"></script>

	<form action="{{ url('/admin/addCompetition') }}"
	        method="post"
	        enctype="multipart/form-data"
			>
		<table class="table table-hover table-bordered ">
			<tr>
				<td width="150px">赛事名称</td>
				<td width="600px"><input class="form-control" id="name" name = "name" type="text" value=""></td>
			</tr>
            <tr>
				<td width="150px">赛事短标题</td>
				<td width="600px"><input class="form-control" id="name_short" name = "name_short" type="text" value=""></td>
			</tr>
			<tr>
				<td>分类</td>
				<td>
					<select name="pid" id="pid">
						<option value="0">其他朗诵会</option>
                        <option value="1">官方朗诵会</option>
						<option value="2">社团朗诵会</option>
						<option value="3">名人朗诵会</option>
                        <option value="4">高端赛事</option>
                        <option value="5">普通赛事</option>
                        <option value="6">诗文高端赛事</option>
                        <option value="7">诗文普通赛事</option>
					</select>
				</td>
			</tr>
            <tr id="price_tr" style="display:none;">
            	<td>收费内容：</td>
                <td>
                名称：<input type="text" name="goods_name" id="goods_name" style="width:300px;"/><br />
                价格：<input type="text" name="goods_price" id="goods_price" style="width:100px;" />元<br />
                </td>
            </tr>
			<tr>
				<td>主图</td>
				<td>
					<input class="file" id="pic0" name="pic0" type="file">
				</td>
			</tr>
			<tr>
				<td>轮播图</td>
				<td>
					<input class="file" id="pic1" name="pic1" type="file"><br/>
					<input class="file" id="pic2" name="pic2" type="file"><br/>
					<input class="file" id="pic3" name="pic3" type="file"><br/>
					<input class="file" id="pic4" name="pic4" type="file"><br/>
					<input class="file" id="pic5" name="pic5" type="file">
				</td>
			</tr>
			<tr>
				<td>排序</td>
				<td>
					<input class="form-control" id="sort" name = "sort" type="text" value="" />		  	
				</td>
			</tr>
			<tr>
				<td>有/没有月榜</td>
				<td>
					<input type="radio" name="monthflag"  value="0" checked>无
				  	<input type="radio" name="monthflag"  value="1">有
				</td>
			</tr>
			<tr>
				<td>开始时间(必填)</td>
				<td>
				  	<input type="text" name="starttime" id="starttime" value="">
				</td>
			</tr>
			<tr>
				<td>结束时间(必填)</td>
				<td>
				  	<input type="text" name="endtime" id="endtime" value="">
				</td>
			</tr>
            <tr>
				<td>是否支持邀请码</td>
				<td>
                
                <input type="radio" name="has_invitation" class="invitationcode" value="0" checked="checked" id="aa0" /><label for="aa0">无邀请码</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="has_invitation" class="invitationcode" value="1"  id="aa1" /><label for="aa1">有邀请码</label>
                </td>
			</tr>
            <tr id="from_content_tr" style="display:none;">
				<td>邀请码说明备注</td>
				<td>
                <textarea name="code_content" id="code_content" style="width:400px; height:80px;"></textarea>
                
                </td>
			</tr>
            <tr>
				<td>服从条款标题</td>
				<td><input class="form-control" id="clause_title" name = "clause_title" type="text" value=""></td>
			</tr>
            <tr>
            	<td>服从条款</td>
                <td>
                <textarea name="clause" id="clause" style="width:600px; height:120px;"></textarea>
                </td>
            </tr>
			<tr text-align="center">
				<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="添加" /></td>
			</tr>
		</table>
	</form>
<script type="text/javascript">
//编辑器
var editor;
KindEditor.ready(function(K) {
	editor = K.create('textarea[name="clause"]', {
		allowFileManager : true,
		minWidth:800,
		minHeight:240
	});
});
	//分别检测主标题，副标题，下载地址，软件大小是否为空
	$('#name').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="nameerror" style="color:red;font-size=10px"> *标题不能为空</span>';
			$('#nameerror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#nameerror').remove();
		}
	});

	$('#sort').focusout(function() {
		var title = $(this).val();
		if(title.length<=0) {
			str = '<span id="sorterror" style="color:red;font-size=10px"> *排列顺序不能为空</span>';
			$('#sorterror').remove();
			$(this).parent().prev().append(str);
			return;
		} else {
			$('#sorterror').remove();
		}
	});


//隐藏收费区
$("#pid").change(function(){
	var pid=$(this).val();
	if(pid==4 || pid == 6 || pid == 7){
		$("#price_tr").show();	
	}else{
		$("#price_tr").hide();
	}
});
//邀请码
$(".invitationcode").click(function(){
	var _val=$(this).val();
	if(_val==1){
		$("#from_content_tr").show();
	}else{
		$("#from_content_tr").hide();
	}
});

$('#starttime').datepicker();
$('#endtime').datepicker();
</script>
@stop

