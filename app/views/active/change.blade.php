@extends('layouts.adIndex')
@section('title')
	添加活跃信息
@stop
@section('crumbs')
	添加活跃信息
@stop
@section('content')
<form action="{{ url('/admin/dochangeActive') }}"   method="post" >
  <table class="table table-hover table-bordered " >
    <tr align="center">
      <td >月份</td>
      <td width="50%"   > <?=$one['time']?></td>
    </tr>
   <tr align="center">
      <td >总用户</td>
      <td width="50%"><input type="text" name='alluser' id='alluser'  value= "<?=$one['alluser']?>"></td>
    </tr>
    <tr align="center">
      <td>日均活跃数</td>
      <td width="50%"   ><input type="text" name='day_hot' id='day_hot' onkeyup="dayhot()" value= "<?=$one['day_hot']?>"></td>
    </tr>
    <tr align="center">
      <td>周均活跃数</td>
      <td width="50%"><input type="text" name='week_hot' id='week_hot'  onkeyup="weekhot()" value= "<?=$one['week_hot']?>"></td>
    </tr>
     <tr align="center">
      <td >月活跃数</td>
      <td width="50%"   ><input type="text" name='mouth_hot' id='mouth_hot' value= "<?=$one['mouth_hot']?>"></td>
    </tr>
     <tr align="center">
      <td >日活跃率(%)</td>
      <td width="50%"   ><input type="text" name='day_per' id='day_per' onkeyup="dayper()" value= "<?=$one['day_per']?>"></td>
    </tr>
    <tr align="center">
      <td>周活跃率(%)</td>
      <td width="50%"   ><input type="text" name='week_per' id='week_per' onkeyup="weekper()" value= "<?=$one['week_per']?>">
      <input type="hidden" name='id'  value= "<?=$one['id']?>">
      </td>
    </tr>
    <tr align="center">
    d
        <td colspan="2" >  <input  class="btn btn-primary" style=" margin-left:-180px"  type="submit" value="提交"/>&nbsp;&nbsp;&nbsp;&nbsp;<input  class="btn btn-info" onclick="back()"  type="button"  value="返回"/></td>
    </td></tr> 
    
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