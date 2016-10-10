@extends('layouts.adIndex')
@section('title')
	伴奏列表
@stop
@section('search')
	<form action="/admin/poemList" method='get'>
		<table>
			<tr>
				<td>伴奏Id</td>
				<td style="width:100px"><input id = 'id' name="id" type='text' value="{{$id}}" class="form-control" /></td>
                <td>伴奏名称</td>
				<td style="width:300px"><input id = 'poemname' name="poemname" type='text' value="{{$poemname}}" class="form-control" /></td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp;&nbsp;
				</td>
                
                
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	伴奏列表
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th></th>
            <th>伴奏id</th>
			<th>伴奏</th>
			<th>下载<i class="icon-search"></i></th>
			<th>读名</th>
			<th>写名</th>
			<th>时长</th>
			<th>首字母</th>
			<th>全拼</th>
			<th>诗别名</th>
			<th>操作</th>
		</tr>
		@foreach ($poemList as $item)
		<tr>
			<td><input type="checkbox" name="ck_id" class="ck_id" value="{{$item['id']}}" /></td>
            <td>{{$item['id']}}</td>
			<td>{{$item['name']}}</td>
			<td><input type="text" name="downnum" data-value="{{$item['id']}}" class="downnum forum-control" value='{{$item['downnum']}}' style="width:100px;" /></td>
			<td>{{$item['readername']}}</td>
			<td>{{$item['writername']}}</td>
			<td>{{$item['duration']}}</td>
			<td><input type=“text" class="spelling" id="spelling" data-value="{{$item['id']}}" name="spelling" value="{{$item['spelling']}}" /></td>
			<td><input type="text" class="allchar" id="allchar" data-value="{{$item['id']}}" name="allchar" value="{{$item['allchar']}}"/></td>
			<td>{{$item['aliasname']}}</td>
			<td>
            	<a href="/admin/updatePoem?id={{$item["id"]}}" target="_blank">修改</a>
			</td>
			
		</tr>
		@endforeach
	</table>
	{{ $poemList->appends(array('poemname'=>$poemname,'id'=>$id))->links(); }}
   <div>
    <input type="checkbox" id="ck_all" name="ck_ids" />全选&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="text" name="downnum" id="downnum" style="width:100px;"/>
	<input type="button" name="a" id="bt_down2" value="下载数量" />
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="button" name="a" id="bt_down" value="全部下载数量" />
    </div>
    
<script type="text/javascript">
function get_allchecked(){
	var arr_id="";
	var checkedObj = $('input:checkbox[name="ck_id"]:checked'); //获取当前checked的value值 如果选中多个则循环 
	var i=1;
	checkedObj.each(function() { 
		var isCheck = this.value; 
		if(i==1){
			arr_id += isCheck;
		}else{
			arr_id += "," + isCheck;
		}
		i++;
	});
	return arr_id;
}
$(function() {
	$("#ck_all").click(function(){
		var _ck=$("#ck_all").prop("checked");
		if(_ck==true){
			$(".ck_id").prop("checked",true);
		}else{
			$(".ck_id").prop("checked",false);
		}
	});
	$("#bt_down2").click(function(){
		var num=$("#downnum").val();
		var ids=get_allchecked();
		if(ids!=''){
			$.get("/admin/addPoemDownNum",{num:num,ids:ids},function(data){
				if(data==1){
					window.alert("操作成功");
					window.location.reload();
				}else{
					window.alert("操作失败");
				}
			});
		}else{
			window.alert("请选择操作项");
		}
	});
});

$(function(){
	$('.downnum').each(function() {
		$(this).focusout(function(){
			var name = $(this).val();
			var poemid = $(this).attr('data-value');
			if(name==null || name=='' || poemid==null || poemid=='')
			{
				alert('参数错误，请重试');
				return;
			}
			$.post('/admin/modifyPoemName',{poemid:poemid,name:name,type:5},function(data){
				if('error' == data) {
					alert('修改下载次数失败');
				}else{
					location.reload();
				}
			})
		});
	});
})
//增加下载量
$("#bt_down").click(function(){
	var num=$("#downnum").val();
	$.get("/admin/addPoemDownNum",{num:num},function(data){
		if(data==1){
			window.alert("操作成功");
			window.location.reload();
		}else{
			window.alert("操作失败");
		}
	});
});

//修改伴奏首字母
$(".spelling").each(function(){
	$(this).focusout(function(){
		var spelling = $(this).val();
		var poemid = $(this).attr('data-value');
		$.post('/admin/modifyPoemChar',{spelling:spelling,poemid:poemid,flag:1},function(data){
				if(data==1)
				{
					window.alert('操作成功');
					window.location.reload();
				}
				else
				{
					window.alert('操作失败');
				}
		})
		});
});

//修改伴奏拼音
$('.allchar').each(function(){
	$(this).focusout(function(){
		var spelling = $(this).val();
		var poemid = $(this).attr('data-value');
		$.post('/admin/modifyPoemChar',{spelling:spelling,poemid:poemid,flag:2},function(data){
			if(data==1)
			{
				window.alert('操作成功');
				window.location.reload();
			}
			else
			{
				window.alert('操作失败');
			}
		})
	});
});
</script>
@stop

