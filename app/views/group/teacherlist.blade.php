@extends('layouts.adIndex')
@section('title')
	培训班老师
@stop
@section('search')
	<form action="/admin/teacherGroup" method='get'>
		<table>
			<tr>
				
                <td >
					班级
                </td>
			 
                 <td style="width:250px">
                     <select name="class" class='form-control',style='width:250px;display:inline;margin-left:20px'>
					  <option>请选择</option>
						<?php if($class){ foreach ($class as $key => $value) { ?>
							
							<option value="<?php echo $value['id']?>" <?php if($search ==$value['id'] )echo ' selected' ?>><?php echo $value['name']?></option>
						<?php  }} ?>
					 </select>
				</td>
				<td  style="margin-right:150px">
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>

	
@stop
@section('crumbs')
	培训班老师
@stop
@section('content')
<?php if($search){?>
<?php if($classinfo){?>
	<table class="table table-hover table-bordered ">
		<tr>
			 
			<th>用户id</th>
			<th>真实姓名</th>
			<th>昵称</th>
			<th>性别</th>	
			<th>手机号</th>
			<th>是否加入群组</th>

		</tr>

		<?php if($user_list){ foreach ($user_list as $key => $value) {?>
 
		<tr>

			<th><?=$value['id']?></th>
	 
			<th><?=$value['real_name']?></th>
			<th><?=$value['nick']?></th>
			<th><?=$value['gender']?"男":"女";?></th>
			<th><?=$value['phone']?></th>
			<th><?php if($classinfo['admin']==$value['id']){ ?>
			    本群群主
			<?php }else { if($value['flag']==0){?>
					<button onclick="add(<?=$value['id']?>,<?=$search?>)" class="btn btn-success">加入</button>
				<?php  }else  {?>
					<button onclick="remove(<?=$value['id']?>,<?=$search?>)" class="btn btn-danger">移除</button>
				<?php } }?>
			</th>
		</tr>
				<?php  	}    ?>
		</table>

		<?php } } else{?>
		<h1>	本班级未创建群组，请创建。</h1>
		<?php  } }else{?>
		<h1>	请选择班级</h1>
		<?php  }?>
		<script >
		function add(id,groupid){
				$.post('/admin/addOrDelGroupUser',{uid:id,groupid:groupid,flag:"1"},function(data) {
					if('error' == data) {
						alert('data');
					} else { 
					 
						 location.reload();
					}
				});
			}
		function remove(id,groupid){
				$.post('/admin/addOrDelGroupUser',{uid:id,groupid:groupid,flag:"0"},function(data) {
					if('error' == data) {
						alert('操作失败，请重试');
					} else {		 
						 
					 location.reload();
					}
				});
			}
</script>
@stop

