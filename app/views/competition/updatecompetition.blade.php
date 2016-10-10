@extends('layouts.adIndex')
@section('title')
	修改赛诗图片
@stop
@section('crumbs')
	修改赛诗图片
@stop
@section('content')
<link rel="stylesheet" href="http://www.weinidushi.com.cn/kindeditor/themes/default/default.css" />
<script charset="utf-8" src="http://www.weinidushi.com.cn/kindeditor/kindeditor-min.js"></script>
<script charset="utf-8" src="http://www.weinidushi.com.cn/kindeditor/lang/zh_CN.js"></script>

	<form  method="post" enctype="multipart/form-data">
		<table class="table table-hover table-bordered ">
			<tr>
				<td width="300px">赛事名称</td>
				<td><input type="text" name="name" value="<?php echo $info['name'];?>" /></td>
			</tr>
            <tr>
				<td width="150px">赛事短标题</td>
				<td width="600px"><input class="form-control" id="name_short" name = "name_short" type="text" value="<?php echo $info['name_short'];?>"></td>
			</tr>
            <tr>
				<td colspan="2">主图</td>
			</tr>
            <tr>
				<td width="300px">
                <?php if(!empty($mainpic)){?><img src="<?php echo Config::get('app.url').$mainpic;?>" width="280" /><?php }?>
                </td>
				<td>
                <input class="file" id="mainpic" name="mainpic" type="file">
                </td>
			</tr>
            <tr>
				<td colspan="2">图片列表</td>
			</tr>
            <?php 
			for($i=0;$i<7;$i++){
			?>
			<tr>
				<td>
                <?php if(isset($piclist[$i])){?>
                <img src="<?php echo Config::get('app.url').$piclist[$i]?>" width="280" />
				<a href="javascript:;" onclick="deltu(<?php echo $info['id']?>,<?php echo $i?>);">删除</a>
				<?php }?>
                </td>
				<td>
					<input class="file" id="pic<?php echo $i;?>" name="pic<?php echo $i;?>" type="file"><br/>
				</td>
			</tr>
            <?php }?>
			<tr>
				<td>开始时间(2014-10-11)</td>
				<td>
				  	<input type="text" name="starttime" id="starttime" value="<?php echo date("Y-m-d",$info['starttime']);?>">
				</td>
			</tr>
			<tr>
				<td>结束时间(2014-10-11)</td>
				<td>
				  	<input type="text" name="endtime" id="endtime" value="<?php echo date("Y-m-d",$info['endtime']);?>">
				</td>
			</tr>
            <tr>
				<td>是否支持邀请码</td>
				<td>
                
                <input type="radio" name="has_invitation" class="invitationcode" value="0" <?php echo $info['has_invitation']==0?'checked':'';?> id="aa0" /><label for="aa0">无邀请码</label>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="has_invitation" class="invitationcode" value="1" <?php echo $info['has_invitation']==1?'checked':'';?> id="aa1" /><label for="aa1">有邀请码</label>
                </td>
			</tr>
            <tr id="from_content_tr">
				<td>邀请码说明备注</td>
				<td>
                <textarea name="code_content" id="code_content" style="width:400px; height:80px;"></textarea>
                
                </td>
			</tr>
            <tr>
				<td>排序</td>
				<td>
                <input type="text" name="sort" value="<?php echo $info['sort'];?>" />
                </td>
			</tr>
			<tr text-align="center">
				<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="添加" /></td>
			</tr>
		</table>
	</form>
<script type="text/javascript">
function deltu(id,_index){
	$.get("/admin/delCompetitionPic",{'id':id,'index':_index},function(data){
		if(data==1){
			window.alert("操作成功");
			window.location.reload();
		}else{
			window.alert("操作失败，请稍后再试");
		}
	});
}
</script>
@stop

