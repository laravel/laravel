@extends('layouts.adIndex')
@section('title')
	班级群组管理
@stop
@section('search')
	<form action="/admin/listGroup" method='get'>
		<table>
			<tr>
				
                <td style="width:150px">
					群组名称
                </td>
                 <td style="width:150px">
                    <input type="text" name="name" value="<?php echo $name;?>" />
				</td>
				<td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp;
				</td>
			</tr>
		</table>
	</form>

@stop
@section('crumbs')
	活动聊天房间管理
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>群组名称</th>
            <th>群组信息</th>
			 <th>群组ID</th>
			<th>群主</th>
			<th>排序</th>
			<th>创建时间</th>
			<th>操作</th>
		</tr>
	 <?php  foreach ($info as $key => $value) {  ?>
		 <tr>
			<th><?php echo  $value['id'] ?></th>
			<th><?php echo $value['groupname']  ?></th>
            <th><?php echo $value['groupinfo']  ?></th>
			<th><?php echo $value['groupid']  ?></th>
			<th><?php echo $value['nick']  ?></th>
			<th><input value='<?php echo $value['sort']?>' onkeyup="change(<?php echo  $value['id'] ?>)"  id='<?php echo  $value['id'] ?>' style="width:30px "></th>
			<th><?php echo date("Y-m-d H:i:s",$value['addtime'])  ?></th>
			<th> <button class="btn btn-success"><a href="changeGroup?id=<?php echo  $value['id'] ?>">修改</a></button >&nbsp; <button class="btn btn-danger" onclick="del(<?php echo  $value['id'] ?>)"> 删除</button> </th>
		</tr>
	 <?php  } ?> 
	</table>
	{{ $info->appends(array('name'=>$name))->links()  }}
	<script>
		function change(id){
			var uid=id;
				var sort=$("#"+id).val();
				 $.post('/admin/changeSort',{id:uid,sort:sort},function(data) {
					if('error' === data) {
						alert('操作失败，请重试');
					} else {
				 		location.reload();
					}
				});
			}
		function del(id){
			if(confirm("是否删除本群组？")){

				window.location.href="delGroup?id="+id;

			}


		}
	
	</script>
@stop

