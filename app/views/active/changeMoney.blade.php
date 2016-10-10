@extends('layouts.adIndex')
@section('title')
	添加活跃信息
@stop
@section('crumbs')
	添加活跃信息
@stop
@section('content')
<form action="{{ url('/admin/dochangeMoneyActive') }}"   method="post" >
  <table class="table table-hover table-bordered " >
    <tr align="center">
      <td >月份</td>
      <td width="50%"   > <?=$one['time']?> </td>
    </tr>
   <tr align="center">
      <td >总用户</td>
      <td width="50%"><input type="text" name='alluser' id='alluser'  value= "<?=$one['alluser']?>"></td>
    </tr>
    <tr align="center">
      <td>月消费人数<b style="color:red" >*</b></td>
      <td width="50%"   ><input type="text" name='user_buy' id='user_buy' onkeyup="userbuy()" value= "<?=$one['user_buy']?>"></td>
    </tr>
    <tr align="center">
      <td>月消费总金额<b style="color:red" >*</b></td>
      <td width="50%"><input type="text" name='money' id='money'    value= "<?=$one['money']?>"></td>
    </tr>
     <tr align="center">
      <td>月用户消费比(%)<b style="color:red" >*</b></td>
      <td width="50%"   ><input type="text" name='buy_per' id='buy_per' onkeyup="buyper()" value= "<?=$one['buy_per']?>">
                          <input type="hidden" name='id' value= "<?=$one['id']?>">
      </td>
    </tr>
    <tr align="center">
      
       <td colspan="2" >  <input  class="btn btn-primary" style=" margin-left:-10px"  type="submit" value="提交"/>&nbsp;&nbsp;&nbsp;&nbsp;<input  class="btn btn-info" onclick="back()"  type="button"  value="返回"/></td>
    </td></tr>
  </table>
 

</form>
<script type="text/javascript">


function back(){
  history.go(-1);
}


//输入日活跃人数
  function userbuy(){
    var alluser=$('#alluser').val();
    if(alluser == ""){
        alert("请输入总人数");
    }else{
    var user_buy=$('#user_buy').val();
      //月用户消费比

      var day_per=Number(user_buy)/Number(alluser);
    
      $('#buy_per').val((day_per*100).toFixed(2));
 
  }
}
  //输入日活跃人数
  function buyper(){
    var alluser=$('#alluser').val();
    if(alluser == ""){
        alert("请输入总人数");
    }else{
    var buy_per=$('#buy_per').val();
      //月用户消费比

 

       var day_hot=Math.round((Number(buy_per)/100)*Number(alluser));  
       $('#user_buy').val(day_hot);
 
  }
}
</script>
@stop