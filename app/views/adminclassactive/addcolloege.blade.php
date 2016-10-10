@extends('layouts.adIndex')
@section('title')
	添加学院及年级
@stop
@section('crumbs')
	添加学院及年级
@stop
@section('search')
@stop

@section('content')
{{Form::open(array('url'=>"/admin/addColloegeActive",'method'=>'post'));}}
	<table class="table table-hover table-bordered ">
	 	<tr>
		 	<td>选择分类</td>
			<td>
				<select name="pid">
					<option value="0">增加学院</option>
					<?php foreach ($list as $key => $value) {?>
						<option value="<?=$value['id']?>"><?=$value['name']?></option>
					<?php  }?>
				</select>
			</td>
		</tr>
		<tr>
		 	<td>学院/年级名称</td>
			<td>
				<input type='text' name='name' >
			</td> 
		 </tr>
		 <tr>
		 	<td>描述</td>
			<td>
				<input type='text' name='desc' >
			</td> 
		 </tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('添加',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
<script>
	$('#starttime').datepicker();
	$('#endtime').datepicker();
</script>
@stop

