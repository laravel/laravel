@extends('layouts.adIndex')
@section('title')
	作品列表
@stop
@section('search')
	<form action="/admin/opusList" method='get'>
		<table>
			<tr>
				<td>UID</td>
				<td style="width:100px"><input id = 'uid' name="uid" type='text' value="{{$return['uid']}}" class="form-control" /></td>
                <td>用户昵称</td>
				<td style="width:100px"><input id = 'nick' name="nick" type='text' value="{{$return['nick']}}" class="form-control" /></td>
				<td>作品名称</td>
				<td style="width:200px"><input id="opusname" name="opusname" type="text" value="{{$return['opusname']}}" class="form-control" /></td>
                <td>是否改编</td>
                <td>
                <select name="type" id="type" class="form-control">
                	<option value="-1" <?php if($return['type']==-1){echo "selected";}?> >全部</option>
                    <option value="0" <?php if($return['type']==0){echo "selected";}?>>未改</option>
                    <option value="1" <?php if($return['type']==1){echo "selected";}?>>改编</option>
                </select>
                </td>
                <td>是否听过</td>
                <td>
                <select name="isread" id="isread"  class="form-control">
                	<option value="-1" <?php if($return['isread']==-1){echo "selected";}?> >全部</option>
                    <option value="0" <?php if($return['isread']==0){echo "selected";}?>>未读</option>
                    <option value="1" <?php if($return['isread']==1){echo "selected";}?>>已审</option>
                </select>
                </td>
                <td>是否删除</td>
                <td>
                <select name="isdel" id="isdel"  class="form-control">
                	<option value="-1" <?php if($return['isdel']==-1){echo "selected";}?> >全部</option>
                    <option value="0" <?php if($return['isdel']==0){echo "selected";}?>>正常</option>
                    <option value="1" <?php if($return['isdel']==1){echo "selected";}?>>已删除</option>
                </select>
                </td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />
				</td>
			</tr>
		</table>
	</form>
@stop
@section('crumbs')
	作品列表  <font color="red" style="margin-left:50px">昨日作品总数:</font> {{$poem_num}}
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th><input type="checkbox" class="ckall" name="ckall" id="ckall" /></th>
            <th>用户id</th>
			<th>用户昵称</th>
            <th>性别</th>
			<th>作品id<i class="icon-search"></i></th>
			<th>作品名称</th>
			<th>收听数</th>
			<th>赞数</th>
			<th>转发数</th>
			<th>评论数</th>
			<th>分享数</th>
			<th>作品下载数</th>
			<th>作品时长秒</th>
			<th>添加时间</th>
            <th>试听</th>
            <th>下载</th>
			<th>删除</th>
			<th>移除</th>
		</tr>
		@foreach ($opuslist as $item)
		<tr>
			<td><input type="checkbox" class="ck_id" name="ck_id" value="{{$item['id']}}" /></td>
            <td>{{$item['uid']}}</td>
			<td>{{$item['nick']}}</td>
            <td>
				@if($item['gender'] == 1)
            		男
            	@else
            		女
            	@endif
            </td>
			<td>{{$item['id']}}</td>
			<td>{{$item['name']}}</td>
			<td><input class="lnum form-control" type="text" value='{{$item['lnum']}}' data-old-val='{{$item['lnum']}}' data-id='{{$item['id']}}' data-uid='{{$item['uid']}}' data-type=1 /></td>
			<td><input class="praisenum form-control" type="text" value='{{$item['praisenum']}}' data-old-val='{{$item['praisenum']}}' data-id='{{$item['id']}}' data-uid='{{$item['uid']}}' data-type=2 /></td>
			<td><input class="repostnum form-control" type="text" value='{{$item['repostnum']}}' data-old-val='{{$item['repostnum']}}' data-id='{{$item['id']}}' data-uid='{{$item['uid']}}' data-type=3/></td>
			<td>{{$item['commentnum']}}</td>
			<td>{{$item['sharenum']}}</td>
			<td>{{$item['downnum']}}</td>
			<td>{{$item['opustime']}}</td>
			<td>{{date('Y-m-d H:i',$item['addtime'])}}</td>
            <td><span data-mp="{{$item['url']}}" data-id="{{$item['id']}}" class="readOpus"><a href="javascript:;" <?php echo $item['isread']==1?'style="color:#333;"':'';?>><?php echo $item['isread']==1?'已审':'试听';?></a></span></td>
            <td><a href="{{$item['url']}}" target="_blank" style="color:green;">下载</a></td>
			<td>

					<button class="operator btn btn-mini btn-danger" type="button"  value='{{$item['id']}}|{{$item['uid']}}|0'>彻底删除</button>
		
			</td>
			<td>
				<button class="catremove btn btn-mini btn-danger" type="button" value='{{$item['id']}}'>移除</button>
			</td>
		</tr>
		@endforeach
	</table>
	{{ $opusitem->appends(array('uid'=>$return['uid'],'isdel'=>$return['isdel'],'nick'=>$return['nick'],'opusname'=>$return['opusname']))->links()  }}

<div>
<select name="op_type" id="op_type" class="form-control" style="width:140px;">
	<option value="1">按选择中结果</option>
    <option value="2">查询全部结果</option>
</select>
</div>
<div>
随机数区间：
<input type="text" id="min_num" name="min_num" class="form-control" style="width:100px" />-<input type="text" id="max_num" name="max_num" class="form-control" style="width:100px" /><br />
<input type="button" value="收听数" data-name="收听数" class="btn btn-success agree" data-id="lnum" />&nbsp;&nbsp;&nbsp;
<input type="button" value="赞数" data-name="赞数" class="btn btn-success agree" data-id="praisenum" />&nbsp;&nbsp;&nbsp;
<input type="button" value="转发数" data-name="转发数" class="btn btn-success agree" data-id="repostnum" />&nbsp;&nbsp;&nbsp;
</div>
    
<script type="text/javascript">
$(".readOpus").click(function(){
	var _this=$(this);
	var mp=_this.attr("data-mp");
	var id=_this.attr("data-id");
	$.get("/admin/readOpus",{id:id,mp:mp},function(data){
		var html='<audio controls="controls"><source src="'+mp+'" type="audio/mpeg">你的浏览器不支持html5的audio标签</audio>';
		_this.replaceWith(html);
	});
});	
function get_allchecked(name_id){
	name_id   =   ( name_id == void 0 ? "view" : name_id );
	var arr_id="";
	var checkedObj = $('input:checkbox[name="'+name_id+'"]:checked'); //获取当前checked的value值 如果选中多个则循环 
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
		//全选
		$(".ckall").click(function(){
			var ck=$(this).prop("checked");
			if(ck==true){
				$(".ck_id").prop("checked",true);
			}else{
				$(".ck_id").prop("checked",false);
			}
		});
		//按钮
		$(".agree").click(function(){
			var op_type=$("#op_type").val();
			var ids;
			if(op_type==1){
				ids=get_allchecked('ck_id');
				if(ids==''){
					window.alert("请先选择要操作的项");
					return false;
				}
			}
			
			var type = $(this).attr("data-id");
			var min_num=$("#min_num").val();
			var max_num=$("#max_num").val();
			var uid = $("#uid").val();
			var nick = $("#nick").val();
			var opusname = $("#opusname").val();
			var _a=op_type==1?'选中':'全部';
			var _v=$(this).attr("data-name");
			if(confirm('确定要增加'+_a+'作品的'+_v+'['+min_num+'-'+max_num+']吗？')){
				$.get("/admin/addOpusNum",{'op_type':op_type,'ids':ids,'type':type,'min_num':min_num,'max_num':max_num,'uid':uid,'nick':nick,'opusname':opusname},function(data){
					if(data==1){
						window.alert("操作成功");
						window.location.reload();
					}else{
						window.alert("操作失败");
					}
				});
			}
			
		});
		$('.operator').each(function(){
			$(this).click(function() {
				if(confirm("彻底删除,无法恢复,是否继续？")){
				var uidSign = $(this).val();
				var arr = uidSign.split('|');
				var opusid = arr[0];
				var uid = arr[1];

				$.post('/admin/delOrDelOpus',{opusid:opusid,uid:uid},function(data) {
					if( data=="false") {
						alert('操作失败，请重试');
					} else {
						alert(data);
						location.reload();
					}
				});}
			});
			
		});
		$('.lnum').each(function(){
			$(this).change(function(){
				//修改后的收听数
				var num = $(this).val();
				//修改的作品id
				var id = $(this).attr('data-id');
				//修改前的值
				var old_val = $(this).attr('data-old-val');
				//用户id
				var uid = $(this).attr('data-uid');
				var type = $(this).attr('data-type');
				$.post('/admin/modify_opus_args',{id:id,num:num,old_val:old_val,type:type,uid:uid},function(data){
					if(data=='error')
					{
						alert("修改错误，请重试");
						return;
					}
					else
					{
						alert('修改收听数成功');
						location.reload();
					}
				});
			})
		});
		$('.praisenum').each(function(){
			$(this).change(function(){
				//修改后的收听数
				var num = $(this).val();
				//修改的作品id
				var id = $(this).attr('data-id');
				//修改前的值
				var old_val = $(this).attr('data-old-val');
				//用户id
				var uid = $(this).attr('data-uid');
				var type = $(this).attr('data-type');
				$.post('/admin/modify_opus_args',{id:id,num:num,old_val:old_val,type:type,uid:uid},function(data){
					if(data=='error')
					{
						alert("修改错误，请重试");
						return;
					}
					else
					{
						alert('修改赞数成功');
						location.reload();
					}
				});
			})
		});
		$('.repostnum').each(function(){
			$(this).change(function(){
				//修改后的收听数
				var num = $(this).val();
				//修改的作品id
				var id = $(this).attr('data-id');
				//修改前的值
				var old_val = $(this).attr('data-old-val');
				//用户id
				var uid = $(this).attr('data-uid');
				var type = $(this).attr('data-type');
				$.post('/admin/modify_opus_args',{id:id,num:num,old_val:old_val,type:type,uid:uid},function(data){
					if(data=='error')
					{
						alert("修改错误，请重试");
						return;
					}
					else
					{
						alert('修改转发数成功');
						location.reload();
					}
				});
			})
		});

		//将作品从比赛中移除
		$('.catremove').click(function(){
			var opusid = $(this).val();
			if(confirm('确定从分类中移除?')){
				$.ajax({
					url:'/admin/catremove',
					type:'GET',
					async:false,
					data:{opusid:opusid},
					success:function(data)
					{
						if(data == 1){
							alert('移除成功');
							// $(this).parent().parent().fadeOut('slow');
						}else{
							alert('该作品没有参加赛事');
							return false;
						}
					}
				});
			}else{
				return false;
			}
		});
	});
</script>
@stop

