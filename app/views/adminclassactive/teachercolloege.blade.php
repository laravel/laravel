@extends('layouts.adIndex')
@section('title')
	年级or班级老师
@stop
@section('crumbs')
	年级or班级老师
@stop
@section('search')
<form action="/admin/teacherActive" method="post">
	<table>
 		<input type="hidden" name="pid"  value="<?php echo  $id ?>">
	<tr>
		<td>用户</td>
		<td>id</td><td><input type="test" name="uid"    value="<?php if($search) echo $search['uid'] ?>" class="form-control" style="width:100px"></td>
		<td>昵称</td><td><input type="test" name="nick" value="<?php if($search) echo $search['nick'] ?>" class="form-control" style="width:100px"></td>
		<td>真实姓名</td><td><input type="test" name="real_name" value="<?php if($search) echo $search['real_name'] ?>" class="form-control" style="width:100px"></td>
		<td> <button type="submit" class="btn btn-success">筛选</button> </td>
	</tr>
	</table>
	   
 	<?php if($find_user){ ?>
	<br>
	<table class="table table-hover table-bordered " algin="middle">
	<tr>
		<td>用户id</td>
		<td>用户昵称</td>
		<td>真实姓名</td>
		<td>手机号码</td>
		<td>用户头像</td>
		<td>操作</td>
	</tr>
 		 <?php foreach ($find_user as $key => $value) {?>
	<tr >
		<td><?=$value['id']?></td>
		<td><?=$value['nick']?></td>
		<td><?=$value['real_name']?$value['real_name']:''?></td>
		<td><?=$value['phone']?></td>
		<td><img src='<?=$url.trim($value['sportrait'],".")?>' alt='头像' width="60" height="60"></td>
		<?if ($id){?>
			<td> <button  onclick="add(<?=$value['id']?>)"  class="btn btn-primary">添加</button> </td>
		<?php } else{?>
		<td>请选择学院</td>
			<?php } ?>
	</tr>
	<?php   }} ?>
 </table>
</form>
@stop
@section('content')
 <form action="/admin/teacherActive" method="post">
 <table>
<tr>
	<td>选择学院 </td>
	
			<input type="hidden" name="uid"    value="<?php if($search) echo $search['uid'] ?>">
			<input type="hidden" name="nick"   value="<?php if($search) echo $search['nick'] ?>">
			<input type="hidden"  name="real_name" value="<?php if($search) echo $search['real_name'] ?>">
			<input type="hidden" name="uid"    value="<?php if($search) echo $search['uid'] ?>">

		<td> <select id='pid' name="pid" class="form-control" style="width:200px">
				<option value="">请选择</option>
				<?php foreach ($list as $key => $value) { ?>
					<option value="<?=$value['id']?>" <?php if($id==$value['id']) echo ' selected'; ?>> <?=$value['name']?></option>

			<?php  	} ?> 
			</select></td>
	 
<td>  <button type="submit" class="btn btn-success">筛选</button>  </td>
</tr>
</table>	
 </form>

<hr>

<table class="table table-hover table-bordered ">

 <?php if ($user) {  ?>
 <tr>
	<th colspan='6' >本学院老师列表</th>
</tr>
	<tr>
	<td>用户id</td>
	<td>用户昵称</td>
	<td>真实姓名</td>	
	<td>手机号码</td>	
	<td>用户头像</td>
	<td>操作</td>
</tr>
 <?php foreach ($user as $key => $value) { ?>	 
<tr>
	<td><?=$value['id']?></td>
	<td><?=$value['nick']?></td>
	<td><?=$value['real_name']?$value['real_name']:'无'?></td>
		<td><?=$value['phone']?></td>
	<td><img src='<?=$url.trim($value['sportrait'],".")?>' alt='头像' width="60" height="60"></td>
	<td> <button onclick=del('<?=$value['id']?>') class="btn btn-danger">移除</button> </td>
</tr>

 
 	<?php  }?>

	 	</table>
		 {{$teacher_list->appends(array('pid' => $id,'nick'=>$search['nick'],'uid'=>$search['uid'],'real_name'=>$search['real_name']))->links();}}
	 <?php } ?>
<script>

function add(id){
	var class_id = $('#pid').val();
	if(confirm("是否添加？")){
			$.post('/admin/addteacherActive',{id:id,class_id:class_id},function(data) {
					if('true' === data) {
						
					} else {
					 	alert(data);
					}
				});
			}location.reload();
}
 
 function del(id){
	var class_id = $('#pid').val();
	if(confirm("是否移除？")){
			$.post('/admin/delteacherActive',{id:id,class_id:class_id},function(data) {
					if('true' === data) {
					 	
					 } else {
					 alert(data);
					 }
				});
			}
	location.reload();
}
//  /admin/addteacherActive

</script>
	 

	 
@stop

