@extends('layouts.adIndex')
@section('title')
	参赛作品列表
@stop
@section('crumbs')
	参赛作品列表
@stop

@section('search')
<form action="/admin/matchOpusList" method='get'>
	<table  style="width:90%; margin-bottom:20px;">
        <tr>
        	<td>选择赛事</td>
            <td>
            <select name="competitionid" id="competitionid" class="form-control">
            	<option value="0">所有赛事</option>
                <?php foreach($all_matchs as $k=>$v){?>
                <option value="<?php echo $k;?>" <?php echo $k==$data["competitionid"]?"selected":"";?>><?php echo $v;?></option>
                <?php }?>
            </select>
            </td>
            <td>UID：</td>
            <td><input name="uid" type='text' id="uid" value="{{$data['uid']}}" class="form-control" style="width:120px;" /></td>
            <td>用户昵称：</td>
            <td><input name="nick_name" type='text' id="nick_name" value="{{$data['nick_name']}}" class="form-control" style="width:160px;" /></td>
            <td>作品名称：</td>
            <td><input name="name" type='text' id="name" value="{{$data['name']}}" class="form-control" style="width:160px;" /></td>
            <td>
            <input type="submit" value="搜索" class="btn btn-mini btn-success" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            <a href="http://www.weinidushi.com.cn/admin/matchOpusListXls" id="btn-dao" target="_blank">导出xls</a>
            </td>
        </tr>
    </table>
</form>
<script language="javascript">
$("#btn-dao").click(function(){
	var uid=$("#uid").val();
	var name=$("#name").val();
	var nick_name=$("#nick_name").val();
	var competitionid=$("#competitionid").val();
	
	var url="http://www.weinidushi.com.cn/admin/matchOpusListXls";
	var url_str="?uid="+uid;
	url_str+="&name="+name;
	url_str+="&nick_name="+nick_name;
	url_str+="&competitionid="+competitionid;
	
	$(this).attr("href",url+url_str);
	return true;
	
});
</script>   
@stop

@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>作品ID</th>
			<th>作品名称<i class="icon-search"></i></th>
			<th>用户ID</th>
			<th>用户昵称</th>
            <th>性别</th>
            <th>赛事名称</th>
			<th>作品收听数</th>
			<th>作品赞数</th>
			<th>作品转发数</th>
			<th>作品评论数</th>
			<th>作品下载数</th>
			<th>作品时长</th>
			<th>作品发布时间</th>
            <th>试听地址</th>
            <th>下载</th>
			<th>删除</th>
		</tr>
		@foreach ($relList as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['name']}}</td>
			<td>{{$item['uid']}}</td>
			<td style="width:150px">{{$item['nick']}}</td>
            <td>
            @if($item['gender']==1)
                男
            @else
                女
            @endif
            </td>
			<td>{{$all_matchs[$item['competitionid']]}}</td>
            <td>{{$item['lnum']}}</td>
			<td>{{$item['praisenum']}}</td>
			<td>{{$item['repostnum']}}</td>
			<td>{{$item['commentnum']}}</td>
			<td>{{$item['downnum']}}</td>
			<td>{{$item['opustime']}}</td>
			<td>{{date('Y/m/d H:i',$item['addtime'])}}</td>
            <td><span data-mp="{{$item['url']}}" data-id="{{$item['id']}}" class="readOpus"><a href="javascript:;" <?php echo $item['isread']==1?'style="color:#333;"':'';?>><?php echo $item['isread']==1?'已读':'试听';?></a></span></td>
            <td><a href="{{$item['url']}}" target="_blank" style="color:green;">下载</a></td>
			<td>
            <button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|{{$item['uid']}}'>删除作品</button>
            <button class="catremove btn btn-mini btn-danger" type="button" data-cid="{{$item['competitionid']}}" data-id="{{$item['id']}}" >移除</button>
            </td>
		</tr>
		@endforeach
	</table>
    {{ $relList->appends(array('name'=>$data['name'],'uid'=>$data['uid'],'nick_name'=>$data['nick_name'],'competitionid'=>$data['competitionid']))->links()  }}
    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$data['total']}}</em> 条记录</a></li></ul>
<script type="text/javascript">
$(function(){
	$(".readOpus").click(function(){
		var _this=$(this);
		var mp=_this.attr("data-mp");
		var id=_this.attr("data-id");
		$.get("/admin/readOpus",{id:id,mp:mp},function(data){
			var html='<audio controls="controls"><source src="'+mp+'" type="audio/mpeg">你的浏览器不支持html5的audio标签</audio>';
			_this.replaceWith(html);
		});
	});	
	$('.operator').each(function(){
		$(this).click(function(){
			var str = $(this).val();
			var arr = str.split('|');
			var opusid = arr[0];
			var uid = arr[1];
			$.post('/admin/admin_del_opus',{opusid:opusid,uid:uid},function(data)
			{
				if('error'==data)
				{
					alert('操作失败');
				}
				else
				{
					location.reload();
				}
			});
		});
	});
	
	//移除
	$(".catremove").click(function(){
		var cid=$(this).attr("data-cid");
		var opusid=$(this).attr("data-id");
		$.get("/admin/catremove",{opusid:opusid,competitionid:cid},function(data){
			if(data==1){
				window.alert("操作成功!");
				//window.location.reload();
			}else{
				window.alert("操作失败!");
			}
		});
	});
	
});
</script>
@stop


