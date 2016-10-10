@extends('layouts.adIndex')
@section('title')
	添加活跃信息
@stop
@section('search')
<form action="{{ url('/admin/moneyList') }}"   method="post" >
  <table>
    <tr>
      <td>年份</td><td><select name="year" id="name"  class="form-control" style="width:200px"> 
      <?php $now=date("Y",time());  for($i=2014;$i<=$now;$i++){?>
   
        <option value="<?=$i?>" <?php if($year==$i) echo 'selected' ?>><?=$i?></option>
      <?php }?> 
    </select></td>
        <td><input  type="submit" value="筛选" class="btn btn-primary" /></td>       
        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;  &nbsp;&nbsp;&nbsp;&nbsp;</td>       
         <td> <a href="/admin/moneyActive"  >图形页面</a> </td> 
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
      <td>月消费人数</td>
      <td>月消费总金额</td>
      <td>用户消费比(%)</td>

      <td>操作</td>
   </tr>
 <?php foreach ($list as $key => $value) {?>

   <tr>
      <td><?=$value['time']?></td>
      <td><?=$value['alluser']?></td>
      <td><?=$value['user_buy']?></td>
      <td><?=$value['money']?></td>
      <td><?php if($value['alluser']){echo  round( (double)((int)$value['user_buy']/(int)$value['alluser']*100));
                    }else{ 
                    echo 0;
                    }?></td>
      <td><a href="/admin/changeMoneyActive?id=<?=$value['id']?>">修改</a></td>
   
   </tr>
<?php  }?>

</table>

@stop