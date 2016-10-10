@extends('layouts.adIndex')
@section('title')
	活跃信息列表
@stop
@section('search')
<form action="{{ url('/admin/listActive') }}"   method="post" >
  <table>
    <tr>
      <td>年份</td><td><select name="year" class="form-control" style="width:200px"> 
      <?php $now=date("Y",time());  for($i=2014;$i<=$now;$i++){ ?>
   
        <option value="<?=$i?>" <?php if($year==$i) echo 'selected' ?>><?=$i?></option>
      <?php }?> 
    </select></td>
        <td><input class="btn btn-primary"  type="submit" value="筛选"/></td>
        <td>        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   <a href="/admin/addActive">添加</a>   </td>
         <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;</td>       
         <td> <a href="/admin/Activelist"  >图形页面</a> </td> 
    </tr>
  </table>
</form>   
@stop
@section('crumbs')
	添加活跃信息
@stop
@section('content')
<table  class="table table-hover table-bordered ">
 <tr>
      <td>时间</td>
      <td>总人数</td>
      <td>月活跃数</td>
      <td>日活跃数</td>
      <td>日活跃度</td>
      <td>周活跃数</td>
      <td>周活跃度</td>
      <td>操作</td>
   </tr>
 <?php foreach ($list as $key => $value) {?>

   <tr>
      <td><?=$value['time']?></td>
      <td><?=$value['alluser']?></td>
      <td><?=$value['mouth_hot']?></td>
      <td><?=$value['day_hot']?></td>
      <td><?=$value['day_per']?></td>
      <td><?=$value['week_hot']?></td>
      <td><?=$value['week_per']?></td>
      <td><a href="/admin/changeActive?id=<?=$value['id']?>">修改</a></td>
   
   </tr>
<?php  }?>

</table>

@stop