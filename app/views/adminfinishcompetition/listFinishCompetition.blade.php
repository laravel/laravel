@extends('layouts.adIndex')
@section('title')
	审核参赛用户
@stop
@section('search')
	 @include('adminfinishcompetition.search1')
@stop
@section('crumbs')
	审核参赛用户     <b>决赛费总计：<?=$all_price?>元；光盘费总计：<?=$cd_price?>元<b>
@stop

@section('content')
	<table class="table  table-bordered ">
		<tr>
			<th><b>用户id<i class="icon-search"></i></b></th>
			<th><b>用户昵称</b></th>
			<th><b>真实姓名</b></th>
			<th><b>手机号码</b></th>
			<th><b>性别</b></th>
			<th><b>用户平台</b></th>
			<th><b>比赛项目</b></th>
			<th><b>决赛费</b></th>
			<th><b>光盘费</b></th>
			<th><b>交费时间</b></th>
			<th><b>交费平台</b></th>
			<th><b>审核状态</b></th>
		</tr>
  <?php if(isset($info1) && !empty($info1)){ foreach ($info1 as $key => $value) {?>
	  <tr style=" font-weight:normal ">
			<td><?php echo $value['user']['uid']?></td>
			<td><?php echo $value['user']['nick_name']?></td>
			<td><?php echo $value['user']['name']?></td>
			<td><?php echo $value['user']['mobile']?></td>
			<td><?php echo $value['user']['gender']?'男':'女';?></td>
			<th><?php echo $value['order']['plat_from']?'安卓':'ios';?></th>
			<td><?php echo $competitionname;?></td>
			<td><?php echo $value['order']['price']?> </td>
			
			<?php if (isset($value['order']['good_id'])){  ?>
				<?php if($value['order']['attach_id']==$value['order']['good_id'] && $value['order']['good_name']){?>
						<td><?php echo $value['order']['attach_price']?></td>
				<?php }elseif($value['order']['attach_id']!=$value['order']['good_id'] && $value['order']['good_name']){?>
						<td><?php echo "-"?></td>
				<?php }?>
			<?php }else{ ?>
					<td><?php echo "无光盘"?></td>
				<?php }	?>
			<td><?php echo date("Y-m-d H:i:s",$value['order']['updatetime'])?> </td>
			<td><?php switch($value['order']['pay_type']){
					 case  $value['order']['pay_type']==1:
            		  echo "银联";  break; 
 					case  $value['order']['pay_type']==2:
            		 echo "支付宝";  break; 
					case  $value['order']['pay_type']==3:
            		 echo "支付宝网银";  break; 
					case  $value['order']['pay_type']==4:
            		echo "财付通";  break; 
			             
				} ?> </td>
			<td><?php if($value['flag']==0){?>
					<button class="operator btn btn-mini btn-danger" type="button"  data-flag = 1 competition-id="<?=$competitionid?>" value="<?=$value['user']['uid']?>">未审核</button>
				<?php }else {?>
					<button class="operator btn btn-mini btn-success" type="button"  data-flag = 0 competition-id="<?=$competitionid?>" value="<?=$value['user']['uid']?>">已审核</button>
				<?php }?>
			</td>
			
		</tr>
		<tr style=" font-weight:normal ">
				<td colspan=12>
			 
					<?php if(!empty($value['user']['card'])){?>
						身份证号:<b><?php echo $value['user']['card']; ?></b>
					<?php }?>
					<?php if(!empty($value['user']['age'])){?>
						年龄:<b><?php echo $value['user']['age']; ?></b>
					<?php }?>
					<?php if(!empty($data['allprovince'][$value['user']['province_id']])){?>
						省:<b><?php echo $data['allprovince'][$value['user']['province_id']]; ?></b>
					<?php }?>
					<?php if(!empty($data['allcity'][$value['user']['province_id']][$value['user']['city_id']])){?>
						市:<b><?php echo $data['allcity'][$value['user']['province_id']][$value['user']['city_id']]; ?></b>
					<?php }?>
					<?php if(!empty($data['allarea'][$value['user']['city_id']][$value['user']['area_id']])){?>
						区/县:<b><?php echo $data['allarea'][$value['user']['city_id']][$value['user']['area_id']]; ?></b>
							<?php }?>
					<?php if(!empty($value['user']['address'])){?>
						地址:<b><?php echo $value['user']['address']; ?></b>
					<?php }?>
					<?php if(!empty($value['user']['zip'])){?>
						邮编:<b><?php echo $value['user']['zip']; ?></b>
					<?php }?>
					<?php if(!empty($value['user']['email'])){?>
						邮箱:<b><?php echo $value['user']['email']; ?></b>
					<?php }?>
					<br>
					<?php if(!empty($value['user']['note'])){?>
						组合类型:<b><?php echo $value['user']['note']; ?></b>
					<?php }?>
				<h4> 

		 		 
				</h4>
				
				
				</td>
			</tr>
  <?php } ?>
  </table>
  <p></p> 
 		 {{$rs->appends($search)->links();}}
	  <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$rs->appends($search)->getTotal();}}</em> 条记录</a></li></ul>
	  <?php }?>

<script type="text/javascript">
	$('.operator').bind('click',function(data){
		var competitionid = $(this).attr('competition-id');
		var uid = $(this).val();
		var flag = $(this).attr('data-flag');
		var a=confirm("确定进行该操作吗？");
		if(a == true){
			$.post("/admin/modifyFinalFlag",{competitionid:competitionid,uid:uid,flag:flag},function(data){
				if(data==1){
					window.location.reload();
				}else{
					window.alert(data);
				}
			});
		}
	});
</script>
@stop

