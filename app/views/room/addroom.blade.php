@extends('layouts.adIndex')
@section('title')
	添加聊天房间
@stop
@section('crumbs')
	添加聊天房间
@stop
@section('search')

@stop
@section('content')
<script src="http://www.weinidushi.com.cn/js/My97DatePicker/WdatePicker.js"></script>	
	<table class="table table-hover table-bordered ">
		<form action="{{ url('/admin/addRoom') }}" 
				method='post'
				enctype="multipart/form-data"
		>
			<table class="table table-hover table-bordered ">
				<tr>
					<td width="200px">选择活动</td>
					<td>
                    <select name="c_id" id="c_id">
                    	<?php foreach($all_comp as $k=>$v){?>
                        <option value="<?php echo $k;?>"><?php echo $v;?></option>
                        <?php }?>
                    </select>
                    </td>
				</tr>
				<tr>
					<td>房间名称</td>
					<td>
						<input class="form-control input-large" id="hx_name" name = "hx_name" type="text" style="width:300px;">		  	
					</td>
				</tr>
                </tr>
                <tr>
					<td>房间密码</td>
					<td>
						<input class="form-control input-large" id="password" name = "password" type="text" style="width:200px;">		  	
					</td>
				</tr>
                <tr>
					<td>管理员uid</td>
					<td>
						<input class="form-control input-large" id="uid" name = "uid" type="text" style="width:100px;">		  	
					</td>
				<tr>
					<td>关闭时间</td>
					<td><input class="form-control" id="closetime" name="closetime" type="text" style="width:200px;" onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})"></td>
				</tr>
                <tr>
					<td>房间大小</td>
					<td>
						<input class="form-control input-large" id="hx_num" name = "hx_num" type="text" value="300" style="width:100px;">		  	
					</td>
				</tr>
                <tr>
                	<td>房间说明</td>
					<td>
						<textarea name="content" id="content" style="width:400px; height:80px;"></textarea>	
					</td>
                </tr>
				
				<tr text-align="center">
					<td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" name="提交" /></td>
				</tr>
			</table>
		</form>
<script language="javascript">
$("#c_id").change(function(){
	var id = $(this).val();
	var name = $(this).find("option[value="+id+"]").text();
	$("#hx_name").val(name);
});
$("#c_id").change();

</script>
@stop


