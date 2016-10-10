@extends('layouts.adIndex')
@section('title')
	上传伴读xls
@stop

@section('crumbs')
	上传伴读xls
@stop
@section('content')
<script src="/js/My97DatePicker/WdatePicker.js"></script>	

<form id="aaa" name="a" action="" method="post" enctype="multipart/form-data">
<table class="table table-hover table-bordered ">
    <tr>
      <th>计划执行时间</th>
      <th><input type="text" id="plan_time" name="plan_time"  onClick="WdatePicker({dateFmt:'yyyy-MM-dd'})"  /></th>
    </tr>
    <tr>
      <th>文件上传 <a href="/demo/poem.xls">范例</a></th>
      <th><input type="file" id="namexls" name="namexls" /></th>
    </tr>
    <tr>
      <th></th>
      <th><input type="button" id="bt"  name="bt" value="提交" /></th>
    </tr>

</table>
</form>
<script language="javascript">
$("#bt").click(function(){
	var plan_time=$("#plan_time").val();
	if(plan_time==''){
		window.alert("选择计划时间");
	}else{
		$("#aaa").submit();
	}
});
</script>
@stop

