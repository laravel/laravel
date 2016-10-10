@extends('layouts.adIndex')

@section('title')
	班级活动列表
@stop
@section('crumbs')
	班级活动列表
@stop
@section('search')

<form action="/admin/classActiveList" method='post'>
		<table>
			<tr>
				<td>状态</td>
				<td style="width:100px"   >
					<select name="isdel"   class="form-control">
                    	<option >请选择</option>
						<option value="0" <?php if($type==2) echo "selected"; ?>>未删除</option>
						<option value="1" <?php if($type==1) echo "selected"; ?>>删除</option>
					</select>
				</td>
			 
				<td>学院</td>
				<td style="width:150px">
					<select name="college"   class="form-control">
                    	<option value='-1'>全部</option>
	 					<optgroup label='普通班级' style="margin-left:140 "></optgroup>
						 		<option value='0' <?php if($college==0) echo 'selected' ?>  style="margin-left:140 ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;普通班级</option>
								 <?php if($option){foreach ($option  as $key => $value) {?>
				<optgroup label='<?=$key?>' ></optgroup>
				<?php foreach ($value as $k  => $v ) {?>
					 <option value="<?php echo $v['id'];?>" <?php if($college==$v['id']) echo 'selected' ?>  style="margin-left:20">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $v['name'];?></option>		
				
			<?php } } } ?>
					</select>
				</td>
				<td >
					<input type="submit" value='查询' class="btn btn-primary" />
				</td>
			</tr>
		</table>
	</form>
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>活动id</th>
			<th>活动名称</th>
			<th>活动描述</th>
			<th>活动主图</th>
			<th>活动类型</th>
			<th>排序</th>
			<th>是否结束</th>
			<th>状态</th>
			<th>操作</th>
		</tr>
		@foreach($list as $k=>$item)
			<tr>
				<td>{{$item['id']}}</td>
				<td>{{$item["name"]}}</td>
				<td>{{$item["desc"]}}</td>
				<td><?php if(isset($pid_name[$k]['mainpic'])) {?><img  src='<?=$pid_name[$k]['mainpic']?>' width="80" height="80"><?php  }?></td>
				<td>{{$pid_name[$k]['pid_name']}} </td>
				<td><input name='sort' value='<?=$item["sort"]?>' onkeyup="change(<?php echo $item['id'] ?>)"  id='<?php echo  $item['id'] ?>' style="width:40px" /></td>
				<td>
					@if($item['isfinish']==0)
						未结束
					@else
						结束
					@endif
				</td>
				<td>
					@if($item['isdel']==0)
						未删除
					@else
						删除
					@endif
				</td>
				<td><a href="changeClassActive/<?php echo $item['id']?>">修改</a>|<?php if($item['isdel']==0){?> 
				<a onclick="del({{$item['id']}})">删除</a>
				<?php }else{ ?>
						<a onclick="del({{$item['id']}})">还原</a>
				<?php } ?>
				</td>
			</tr>
		@endforeach
	</table>
	{{$list->appends(array('isdel' => $type,'college'=>$college))->links();}}
<script >
		function change(id){
			var uid=id;
				var sort=$("#"+id).val();
				 $.post('/admin/changeSort',{id:uid,sort:sort},function(data){
					if('error' === data) {
						alert('操作失败，请重试');
					} else {
				 		location.reload();
					}
				});
			}
		$('.operator').bind('focusout',function(){
			//操作类型 1 修改商品名称 2,修改商品描述 3修改价格
			var data_type = $(this).attr('data-type');
			var id = $(this).attr('data-id');
			var val = $(this).val();
			var token = $('input[name=_token]').val();
			if(data_type == null || id == null || val == null){
				alert('修改错误，请重试');
				return;
			}
	$.post('/admin/modifyGoodInfo',{data_type:data_type,id:id,val:val,'_token':token},function(data){
		if(data == 1){
			alert('修改成功');
			location.reload();
		}else if(data ==2){
			return;
		}else{
			alert(data);
			return;
		}
	});
});
//删除班级活动
function del(id){
	if(confirm("是否执行操作？")){
 	$.ajax({ 
		 url: "/admin/delClassActive/"+id, 
		 context: document.body,
		 success: function(e){
       	// switch{
		// 	   case 1:alert("删除成功！");
		// 	   break;
		// 	   case 2:alert("删除失败！");
		// 	   break;
		// 	   case 3:alert("还原成功！");
		// 	   break;
		// 	   case 4:alert("还原失败！");
		// 	   break;
		//    }
		if(e==1){
			alert("删除成功！");
			location.reload();
		}else if(e==2){
			alert("删除失败！");
			location.reload();
		}else if(e==3){
			alert("还原成功！");
			location.reload();
		}else if(e==4){
			alert("还原失败！");
			location.reload();
		}
      }});
	}
}
</script>

@stop

