@extends('layouts.adIndex')
@section('title')
	学院及年级列表
@stop
@section('crumbs')
	学院及年级列表
@stop
@section('search')
<form action="/admin/listColloegeActive" method="post">
<table>
	<tr>
		<td>选择学院</td>
		<td><select name="id" class="form-control" style="width:200px">
				<option value="0">请选择</option>
				<?php foreach ($list as $key => $value) { ?>
					<option value="<?=$value['id']?>" <?php if($id==$value['id']) echo ' selected'; ?>> <?=$value['name']?></option>

			<?php  	} ?> 
			</select>
		</td>

		<td> <button type="submit" class="btn btn-success">筛选</button> </td>
	</tr>
	</table>
</form>
@stop

@section('content')
 
	<table class="table table-hover table-bordered ">
		<?php if($college){?>
	 	<tr>
			<td>id</td>
			<td>学院名</td>
			<td>学院描述</td>
			<td>操作</td>
		</tr>
		<?php foreach ($college as $key => $value) {?>
		<tr>
			<td><?=$value['id']?></td>
			<td><?=$value['name']?></td>
			<td><?=$value['desc']?></td>
			<td><?php if($value['isdel']==0){?>
			<button type="submit" class="btn btn-danger" onclick="del(<?=$value['id']?>)">删除</button>
			<?php  }else{?>
			<button type="submit" class="btn btn-success" onclick="nodel(<?=$value['id']?>)">恢复</button>
			<?php }?>
			<button type="submit" class="btn btn-primary" onclick="change(<?=$value['id']?>)">修改</button></td>
		</tr>
		<?php }?>
		 <?php } ?>
	</table>
 <script>
 		function change(id){
			 if(confirm('是否修改？')){
				 window.location="/admin/changeColloegeActive?id="+id;

		 	}
		 }
			 
		 


		function del(id){
			if(confirm("是否移除？")){
			$.post('/admin/delColloegeActive',{id:id,flag:"1"},function(data) {
					if('false' == data) {
						alert('操作失败，请重试');
					} else {
					 
						location.reload();
					}
				});
			}
			 
		}
		function nodel(id){
			if(confirm("是否移除？")){
			$.post('/admin/delColloegeActive',{id:id,flag:"0"},function(data) {
					if('false' == data) {
						alert('操作失败，请重试');
					} else {
						location.reload();
					}
				});
			}
			 
		}
 </script>
@stop

