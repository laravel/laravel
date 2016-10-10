@extends('layouts.adIndex')
@section('title')
	培训班交费学员
@stop
@section('search')
	<form action="/admin/userGroup" method='get'>
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
						<?php  } } ?>
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
	培训班交费学员
@stop
@section('content')
<?php if($search){?>
<?php if($classinfo){?>
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>用户id</th>
			<th>真实姓名</th>
			<th>昵称</th>
			<th>性别</th>	
			<th>手机号</th>
			<th>申请时间</th>
			<th>培训班</th>
			<th>是否交费</th>
			<th>是否加入群组</th>

		</tr>

		<?php if($info){ foreach ($info as $key => $value) {?>
 
		<tr>

			<th><?=$value['id']?></th>
			<th><?=$value['uid']?></th>
			<th><?=$userinfo[$key]['real_name']?></th>
			<th><?=$userinfo[$key]['nick']?></th>
			<th><?=$userinfo[$key]['gender']?></th>
			<th><?=$userinfo[$key]['phone']?></th>
			<th><?=date("Y-m-d H:i:s",$value['updatetime'])?></th>
			<th><?=$value['description']?></th>
			<th>已缴费</th>
			<th> 
			 
			<?php    if($userinfo[$key]['flag']==0){?>
					<button onclick="add(<?=$value['uid']?>,<?=$search?>)" class="btn btn-success">加入</button>
				<?php  }else if($userinfo[$key]['flag']==1) {?>
					<button onclick="remove(<?=$value['uid']?>,<?=$search?>)" class="btn btn-danger">移除</button>
				<?php }  ?>
			</th>
		</tr>
				<?php  	}     ?>
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
						alert('操作失败，请重试');
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

