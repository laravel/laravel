@extends('layouts.adIndex')
@section('title')
	计划任务配置修改
@stop
@section('search')

@stop
@section('crumbs')
	计划任务配置修改
@stop
@section('content')
<form id="aaa" name="aaa" action="/admin/updatePlanConfigDo" method="post">
<input type="hidden" name="id" value="<?php echo $info['id'];?>" />
<table class="table table-hover table-bordered ">
    <tr>
        <td align="right">名称：</td>
        <td><?php echo $info['name'];?></td>
    </tr>
    <?php
    $contens=unserialize($info['contents']);
	?>
    <tr>
        <td align="right">收听数：</td>
        <td>
        <input type="text" name="min_show" value="<?php echo isset($contens['min_show'])?$contens['min_show']:'';?>" />
        -
        <input type="text" name="max_show" value="<?php echo isset($contens['max_show'])?$contens['max_show']:'';?>" />
        </td>
    </tr>
    <tr>
        <td align="right">赞同数：</td>
        <td>
        <input type="text" name="min_agree" value="<?php echo isset($contens['min_agree'])?$contens['min_agree']:'';?>" />
        -
        <input type="text" name="max_agree" value="<?php echo isset($contens['max_agree'])?$contens['max_agree']:'';?>" />
        </td>
    </tr>
    <tr>
        <td align="right">转发数：</td>
        <td>
        <input type="text" name="min_zhuan" value="<?php echo isset($contens['min_zhuan'])?$contens['min_zhuan']:'';?>" />
        -
        <input type="text" name="max_zhuan" value="<?php echo isset($contens['max_zhuan'])?$contens['max_zhuan']:'';?>" />
        </td>
    </tr>
    <tr>
    	<td align="right">状态：</td>
        <td>
        <input type="radio" name="status" value="0" <?php echo $info['status']==0?'checked':'';?> />关闭&nbsp;&nbsp;&nbsp;&nbsp;
        <input type="radio" name="status" value="2" <?php echo $info['status']==2?'checked':'';?> />关闭
        </td>
    </tr>
    <tr>
        <td align="right"></td>
        <td><input type="button" id="sub" value="保存" /></td>
    </tr>
</table>
</form>
<script language="javascript">
$("#sub").click(function(){
	$("#aaa").submit();
})
</script>
@stop

