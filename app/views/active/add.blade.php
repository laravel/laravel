@extends('layouts.adIndex')
@section('title')
	添加活跃信息
@stop
@section('crumbs')
	添加活跃信息
@stop
@section('content')
<form action="{{ url('/admin/doaddActive') }}"   method="post" >
  <table class="table table-hover table-bordered " >
    <tr align="center">
      <td >月份(格式：2016年01月)</td>
      <td   ><input type="text" name='time' id='time'  class="form-control"  style="width:200px"></td>
    </tr>
   <tr align="center">
      <td >总用户</td>
      <td  ><input type="text" name='alluser' id='alluser'  class="form-control" value="<?php echo $user[0]['num'] ?>" style="width:200px"></td>
    </tr>
    <tr align="center">
      <td>日均活跃数</td>
      <td     ><input type="text" name='day_hot' id='day_hot'  class="form-control" onkeyup="dayhot()" style="width:200px"></td>
    </tr>
    <tr align="center">
      <td>周均活跃数</td>
      <td  ><input type="text" name='week_hot' id='week_hot'   class="form-control" onkeyup="weekhot()" style="width:200px"></td>
    </tr>
     <tr align="center">
      <td >月活跃数</td>
      <td     ><input type="text" name='mouth_hot'  class="form-control" id='mouth_hot' style="width:200px"></td>
    </tr>
     <tr align="center">
      <td >日活跃率(%)</td>
      <td     ><input type="text" name='day_per' id='day_per'   class="form-control" onkeyup="dayper()" style="width:200px"></td>
    </tr>
    <tr align="center">
      <td>周活跃率(%)</td>
      <td     ><input type="text" name='week_per' id='week_per'  class="form-control" onkeyup="weekper()" style="width:200px"></td>
    </tr>
 
   <tr align="center">
      <td colspan="2" >  <input  class="btn btn-primary" style=" margin-left:-180px"  type="submit" value="提交"/>&nbsp;&nbsp;&nbsp;&nbsp;<input  class="btn btn-info" onclick="back()"  type="button"  value="返回"/></td>
      
    </tr>
  </table>
  

</form>
<script type="text/javascript">

function back(){
  history.go(-1);
}


//输入日活跃人数
  function dayhot(){
    var alluser=$('#alluser').val();
    if(alluser == ""){
        alert("请输入总人数");
    }else{
      var random=(Math.random()+3.5).toFixed(2);
      var day_hot=$('#day_hot').val();
      var day_per=Number(day_hot)/Number(alluser);
       //日活跃率
      $('#day_per').val((day_per*100).toFixed(2));
       //月活跃人数
      $('#mouth_hot').val(day_hot*30);
       //周活跃人数
      $('#week_hot').val(Math.round(day_hot*random ));
      var week_hot=Number($('#week_hot').val());
       //周活跃率
      $('#week_per').val(Math.round((week_hot*100)/Number(alluser)).toFixed(2));
    }
  }
  //输入日活跃率
  function dayper(){
    var random=(Math.random()+3.5).toFixed(2);
    var alluser=$('#alluser').val();
    if(alluser == ""){
        alert("请输入总人数");
    }else{
      var day_per=$('#day_per').val();
      var day_hot=Math.round((Number(day_per)/100)*Number(alluser));  
      //日活跃人数
      $('#day_hot').val(day_hot);
      //周活跃数
      $('#week_hot').val((Math.round(day_hot*random)));
        var week_hot=Number($('#week_hot').val());
      $('#week_per').val(Math.round((week_hot*100)/Number(alluser)).toFixed(2));
      //月活跃人数
      $('#mouth_hot').val(day_hot*30);
    }
  }
  //输入周活跃人数
  function weekhot(){
    var alluser=$('#alluser').val();
    if(alluser == ""){
        alert("请输入总人数");
    }else{
      var week_hot=$('#week_hot').val();
      var week_per=Number(week_hot)/Number(alluser);  
      $('#week_per').val((week_per*100).toFixed(2));
     
    }
  }
  //输入周活跃率
  function weekper(){
    var alluser=$('#alluser').val();
    if(alluser == ""){
        alert("请输入总人数");
    }else{
      var week_per=$('#week_per').val();
      var week_hot=Math.round((Number(week_per)/100)*Number(alluser));  
      $('#week_hot').val(week_hot);
    }

  }
</script>
@stop