@extends('layouts.adIndex')
@section('title')
	修改学院/年级
@stop
@section('crumbs')
	修改学院/年级
@stop
@section('search')
@stop

@section('content')
{{Form::open(array('url'=>"/admin/changeColloegeActive",'method'=>'post'));}}
	<table class="table table-hover table-bordered ">
		<tr>
		 	<td>学院/年级名称</td>
			<td>
				<input type='text' name='name' value="<?=$college['name']?>" >
			</td> 
		 </tr>
		 <tr>
		 	<td>描述</td>
			<td>
				<input type='text' name='desc' value="<?=$college['desc']?>" >
				<input type='hidden' name='id' value="<?=$college['id']?>" >
			</td> 
		 </tr>
		<tr>
			<td colspan="2" style="text-align:center">{{Form::submit('修改',array('class'=>"btn btn-success"));}}</td>
		</tr>
	</table>
{{ Form::close() }}
<script>
	$('#starttime').datepicker();
	$('#endtime').datepicker();
</script>
@stop

