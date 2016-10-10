@extends('layouts.adIndex')
@section('title')
	订单信息
@stop
@section('crumbs')
	订单信息
@stop
@section('search')
<form action="{{ url('/admin/AppOrderList') }}"   method="post" >
	<table  >
		<tr>
			<td>订单号</td><td><input type='text' name="orderid"  id="orderid" value="<?=$orderid?>"   class="form-control"  /></td>
			<td>购买人</td><td><input type='text' name="nick"    id="nick" value="<?=$nick?>"  class="form-control" /></td>
			<td>商品名</td><td><input type='text' name="good" id="good"  value="<?=$good?>"  class="form-control"  /></td>
			<td>客户平台</td><td><select name='plat_from' id="plat_from" class="form-control">
									<option value=-1>全部</option>
									<option value=0 <?php if($plat_from == 0) echo 'selected'?>>IOS平台</option>
					  				<option value=1 <?php if($plat_from == 1) echo 'selected'?>>安卓平台</option>
								</select></td>
		</tr>
		<tr>
			<td>支付平台</td><td><select name='pay_type'  id="pay_type" class="form-control">
									<option value=0>全部</option>
									<option value=1 <?php if($pay_type == 1) echo 'selected'?>>银联支付</option>
									<option value=2 <?php if($pay_type == 2) echo 'selected'?>>支付宝支付</option>
									<option value=3 <?php if($pay_type == 3) echo 'selected'?>>支付宝网页</option>
									<option value=4 <?php if($pay_type == 4) echo 'selected'?>>微信(财付通)</option>
								</select></td>
			<td>开始时间</td><td><input type='text' name="starttime" id="starttime"  value="<?=$starttime?>" class="form-control hasDatepicker" id='starttime'  /></td>
			<td>结束时间</td><td><input type='text' name="endtime"  id="endtime"   value="<?=$endtime?>" class="form-control hasDatepicker" id='endtime' /></td>
			<td>收货人</td><td><input type='text' name="buyname"  id="buyname"  value="<?=$buyname?>"  class="form-control"  /></td>	
		</tr>
		<tr>
			<td  >省份：</td>
			               <td>
				               <select name="province_id" id="province_id" class="form-control" >
				                	<option value="">全部</option>
				                    <?php foreach($allprovince as $k=>$v){?>
				                	<option value="<?php echo $k;?>" <?php echo $province_id==$k?"selected":"";?>><?php echo $v;?></option>
				                    <?php }?>
				                </select>
			                </td>
				<td  >城市：</td>
			               <td>
				                <select name="city_id" id="city_id" class="form-control" >
				                	<option value="">全部</option>
				                    <?php
				                    if(isset($allcity[$province_id])){
										foreach($allcity[$province_id] as $k=>$v){ ?>
								<option value="<?=$k?>"  <?php echo $city_id==$k?"selected":"";?>  ><?=$v?></option>
									<?php 	} }	?>
				               </select>
			                </td>
				<td>县区：</td>
								<td>
				               <select name="area_id" id="area_id" class="form-control" >
				                	<option value="">全部</option>
				                    <?php
				                    if(isset($allarea[$city_id])){
										foreach($allarea[$city_id] as $k=>$v){?>
											 
											<option value="<?=$k?>"  <?php echo $area_id==$k?"selected":"";?>  ><?=$v?></option>
								<?php 		}
									}
									?>
				                </select>
			                </td>
									 
			        <td>是否发货</td>
								<td>
				               <select name="send"   class="form-control" >
				                	<option value="-1" <?php echo $send==0?"selected":"";?> >全部</option>
										<option value="0" <?php echo $send==0?"selected":"";?> >未发货</option>
											<option value="1" <?php echo $send==1?"selected":"";?> >已发货</option>
				                   
				                </select>
			                </td>
									 
				</tr>	
		<tr align="center">               
			<td colspan="8" align="center"> <button type="submit" class="btn btn-info">筛选</button>  
			 <a  onclick="execl()"  target="_blank"  id='btn-dao' >导出</a></td>
		</tr>
		
	</table>
</form>
@stop
@section('content')

	<table class="table table-hover table-bordered " >
	<tr>
		<th>订单号</th>
		<th>购买人</th>
		<th>商品名</th>
		<th>数量</th>
		<th>购买单价</th>
		<th>原定单价</th>
		<th>总价</th>
		<th>邮费</th>
		<th>客户端平台</th>
		<th>支付平台</th>
		<th>时间</th>
		<th>是否发货</th>
	</tr>
	<?php if($order_list){ foreach($order_list as $k=>$v){?>
		<tr>
			<th><?=$v['orderid']?></th>
			<th><?=$users_info[$v['uid']]?></th>
			<th><?=$good_name[$v['goods_id']]?></th>
			<th><?=$v['num']?></th>
			<th><?=$v['price']?></th>
			<th><?=$v['old_price']?></th>
			<th><?=$v['total_price']?></th>
			<th><?=$v['attach_price']?></th>
			<th><?php echo $v['plat_from']?'安卓':'苹果'; ?></th>
			<th><?php if($v['pay_type']==1){echo '银联';}elseif($v['pay_type']==2){ echo '支付宝';}elseif($v['pay_type']==3){ echo '支付宝网页';}elseif($v['pay_type']==4){echo '财付通';} ?></th>
			<th><?=date('Y-m-d H:i:s',$v['updatetime'])?></th>
			
			<th><?php if($v['send_out']==1){ ?>
			<button class="btn btn-success"  onclick=change("<?=$v['id']?>",1) >已发货</button>
		 <?php   } else{ ?>
		 	<button  class="btn btn-danger"  onclick=change("<?=$v['id']?>",0)>未发货</button>
		 <?php }   ?>
		 </th>
	

 </tr>
 <tr>
	<td colspan='12'>
	 <p>地址：<?=$address_info[$v['address_id']]['province']?><?=$address_info[$v['address_id']]['city']?><?=$address_info[$v['address_id']]['area']?><?=$address_info[$v['address_id']]['address']?></p>
	 <p>收件人:<?=$address_info[$v['address_id']]['name']?></p>
	 <p>收件人电话:<?=$address_info[$v['address_id']]['tel']?></p>
	</td>
	 </tr>
			<?php }  ?>

			
	</table>
	
<?php echo $order_list->appends(array('orderid' => $orderid,'nick'=>$nick,'good'=>$good,'plat_from'=>$plat_from,'send'=>$send
			,'pay_type'=>$pay_type,'starttime'=>$starttime,'endtime'=>$endtime,'buyname'=>$buyname,'province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id
			))->links();  ?>

 <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;"><?php echo $order_list->appends(array('orderid' => $orderid,'nick'=>$nick,'good'=>$good,'plat_from'=>$plat_from,'send'=>$send
			,'pay_type'=>$pay_type,'starttime'=>$starttime,'endtime'=>$endtime,'buyname'=>$buyname,'province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id
			))->getTotal();  	?></em> 条记录</a></li></ul><?php }?>

<script >
		function change(id,flag){
			if(confirm('确认操作？')){
				$.post('/admin/changeOrderList',{id:id,flag:flag},function(data) {
					if("error"== data) {
						alert('请重试');
					} else { 
						 location.reload();
					}
				});
			}
			}
 		$("#province_id").change(function(){
			//重置
			$("#city_id").empty();
			$("#area_id").empty();
			var _val=$(this).val();
			if(_val!=""){
				$.getJSON("/admin/getCity",{province_id:_val},function(data){
					var html="<option value='0'>全部</option>";
					$.each(data, function(i, field){
						html+='<option value="'+field.id+'">'+field.name+'</option>';
					});
					$("#city_id").append(html);
				});
			}
		});
		$("#city_id").change(function(){
			var _val=$(this).val();
			if(_val!=""){
				$.getJSON("/admin/getArea",{city_id:_val},function(data){
					var html="<option>全部</option>";
					$.each(data, function(i, field){
						html+='<option value="'+field.id+'">'+field.name+'</option>';
					});
					$("#area_id").append(html);
				});
			}
		});
	$('#starttime').datepicker();
	$('#endtime').datepicker();
 
   function execl(){
	 
	var orderid=$("#orderid").val();
	var nick=$("#nick").val();
	var good=$("#good").val();
	var plat_from=$("#plat_from").val();
	var pay_type=$("#pay_type").val();
	var starttime=$("#starttime").val();
	var endtime=$("#endtime").val();
	var buyname=$("#buyname").val();
		var province_id=$("#province_id").val();
		var city_id=$("#city_id").val();
		var area_id=$("#area_id").val();

	var url="/admin/execlOrderList";
	var url_str="?orderid="+orderid;
	url_str+="&name="+name;
	url_str+="&nick="+nick;
	url_str+="&good="+good;
	url_str+="&plat_from="+plat_from;
	url_str+="&pay_type="+pay_type;
	url_str+="&starttime="+starttime;
	url_str+="&endtime="+endtime;
	url_str+="&buyname="+buyname;
	url_str+="&province_id="+province_id;
	url_str+="&city_id="+city_id;
	url_str+="&area_id="+area_id;
	
		var tourl=url+url_str;
	$("#btn-dao").attr("href",tourl);

	return true;
}

</script>
@stop