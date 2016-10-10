@extends('layouts.adIndex')
@section('title')
	详情
@stop
@section('crumbs')
	详情
@stop
@section('content')
	<form action ='/admin/permissionsDetail' method = 'post' enctype="multipart/form-data" >
	<table class="table table-hover table-bordered ">
		<tr>
			<tr>
		 	<td>详情</td>
			<td>
				<select name="pid">
					<option value="0">详情列表</option>
					<?php foreach ($permissionsdetail as $k => $v): ?>
					<option value="<?=$v['id']?>"><?=$v['name']?></option>
					<?php endforeach ?>
					</select>
			</td>
		</tr>
		</tr>
		<tr>
			<td width="200px">图片</td>
			<td><input class="form-control input-xxlarge" id="pic" name = "pic" type="file" value=""></td>
		</tr>
		<tr>
			<td>标题</td>
			<td><input class="form-control input-xxlarge" id="title" name = "title" type="text" value=""></td>
		</tr>
		<tr>
			<td>描述</td>
			<td><input class="form-control input-xxlarge" id="desc" name = "desc" type="text" value=""></td>
		</tr>
		<tr text-align="center">
			<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
		</tr>
	</table>
	</form>
	
<script type="text/javascript">
		$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
</script>
@stop

