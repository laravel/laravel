@extends('layouts.adIndex')
@section('title')
	班级老师
@stop
@section('crumbs')
	班级老师
@stop
@section('search')
<form action="/admin/addclassteacherActive" method="post">
<table>
	<tr>
		<td>选择班级<td>
		<td><select name="id" id="id"  class="form-control" style="width:200px"  >
			<option value="">请选择</option>
			<?php if($option){foreach ($option  as $key => $value) {?>
				<optgroup label='<?=$key?>' ></optgroup>
				<?php foreach ($value as $k  => $v ) {?>
					 <option value="<?php echo $v['id'];?>" <?php if($id==$v['id']) echo 'selected' ?>  style="margin-left:20"><?php echo $v['name'];?></option>		
				
			<?php } } } ?>
		</select><td>
	 	<td ><input  class = "btn btn-success" type="submit" name="提交" /></td>
	</tr>
</table>
   
</form>

 
@stop

@section('content')
  
	<?php if($user){?> 
		<table  class="table table-hover table-bordered ">
		<tr>
			<td>用户id</td>
			<td>用户昵称</td>
			<td>真实姓名</td>	
			<td>手机号码</td>	
			<td>用户头像</td>
			<td>操作</td>
		</tr>
		<?php foreach ($user as $key => $value) {?>
		<tr>
			<td><?=$value['id']?></td>
			<td><?=$value['nick']?></td>
			<td><?=$value['real_name']?$value['real_name']:'无'?></td>
				<td><?=$value['phone']?></td>
			<td><img src='<?=$url.trim($value['sportrait'],".")?>' alt='头像' width="60" height="60"></td>
			<?php if($value['flag']){?>
				<td> <button onclick=del('<?=$value['id']?>') class="btn btn-danger">移除</button> </td>
			<?php }else { ?>
				<td> <button onclick=add('<?=$value['id']?>') class="btn btn-success">加入</button> </td>
			<?php } ?>
		</tr>


	<?php }
	echo "</table>";
	 
	}?>

{{$list->appends(array('id' => $id))->links();}}


<script>
function add(id){
	var class_id = $('#id').val();
	if(confirm("是否添加？")){
			$.post('/admin/doaddteacher',{id:id,class_id:class_id,flag:'1'},function(data) {
					if('true' === data) {
					 
						location.reload();
					} else {
					 	alert(data);
						
					}
				});
			}
}
 
function del(id){
	var class_id = $('#id').val();
	if(confirm("是否移除？")){
			$.post('/admin/doaddteacher',{id:id,class_id:class_id,flag:'0'},function(data) {
					if('true' === data) {
						 
						location.reload();
						
					} else {
					 	alert(data);
						
					}
				});
			}
}


</script>
	 

	 
@stop

