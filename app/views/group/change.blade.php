@extends('layouts.adIndex')
@section('title')
	添加群组
@stop
@section('crumbs')
	添加群组
@stop
@section('search')

@stop
@section('content')
<script src="http://www.weinidushi.com.cn/js/My97DatePicker/WdatePicker.js"></script>	
	<table class="table table-hover table-bordered ">
		<form action="{{ url('/admin/dochangeGroup') }}" 
				method='post'
				enctype="multipart/form-data"
		>
			<table class="table table-hover table-bordered ">
				 
				<tr>
					<td>房间名称</td>
					<td>
						<input class="form-control input-large" id="hx_name" name ="name" value="<?php echo $group['groupname'] ?>" type="text" style="width:300px;">		  	
					</td>
				</tr>
				<tr>
					<td>房间头像</td>
					<td>
						<img src="<?php  echo $group['pic'] ?>" alt="头像" width="200" height="200" />
						<input type="file" name="file" id="file" /> 	  			  	
					</td>
				</tr>
                <tr>
               
					<td>人数上限</td>
					<td>
						<input class="form-control input-large" name = "maxusers" type="text" value="<?php echo $group['num'] ?>" style="width:100px;">		  	
					</td>
				</tr>
                <tr>
                	<td>群组描述</td>
					<td>
					<input type="hidden" name="id" value="<?php echo $id ?>">
						<textarea name="desc"  style="width:400px; height:80px;"> <?php echo $group['groupinfo'] ?></textarea>	
					</td>
                </tr>
				
				<tr text-align="center">
					<td  colspan="2"><input  class = "btn btn-danger" type="submit" value="提交" /></td>
				</tr>
			</table>
		</form>
<script language="javascript">
 
</script>
@stop


