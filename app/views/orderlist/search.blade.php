<form action="/admin/orderList/{{$id}}" method='post'>
		<table >
			<tr>
				<td style="width:140px">
					<select class="form-control" id="status" name="status">
					  	<option value="-1">不限</option>
                        <option value=0 <?php if($status == 0) echo 'selected'?>>支付失败</option>
					  	<option value=2 <?php if($status == 2) echo 'selected'?>>支付成功</option>
					</select>
				</td>
				<td style="width:140px">
					<select class="form-control" id="pay_type" name="pay_type">
					  	<option value="-1">不限</option>
                        <option value=1 <?php if($pay_type == 1) echo 'selected'?>>银联支付</option>
					  	<option value=2 <?php if($pay_type == 2) echo 'selected'?>>支付宝支付</option>
					  	<option value=3 <?php if($pay_type == 3) echo 'selected'?>>支付宝网页</option>
					  	<option value=4 <?php if($pay_type == 4) echo 'selected'?>>微信(财付通)</option>
					</select>
				</td>
				<td style="width:100px">
					<select class="form-control" id="plat_from" name="plat_from">
					  	<option value="-1">不限</option>
                        <option value=0 <?php if($plat_from == 0) echo 'selected'?>>IOS平台</option>
					  	<option value=1 <?php if($plat_from == 1) echo 'selected'?>>安卓平台</option>
					</select>
				</td>
                <td style="width:180px">
					<select class="form-control" name="goods_id" id="goods_id">
					  	<option value="-1">不限</option>
                        <?php foreach($all_goods as $k=>$v){?>
                        <option value="<?php echo $k;?>" <?php if($goods_id == $k) echo 'selected'?>><?php echo $v;?></option>
                        <?php }?>
					</select>
				</td>
                <td style="width:50px">
					用户ID
                </td>
                 <td style="width:30px">
                    <input type="text" name="uid" class="form-control"  value="<?php echo $uid?>" style="width:120px"/>
				</td>
				<td>
					<input type="text" id="starttime" name="starttime"  class="form-control"  value="<?php if(!empty($starttime)) {echo $starttime;} else {echo "开始时间";}?>"/>
				</td>
				<td>
					<input type="text" id="endtime" class="form-control"  name="endtime" value="<?php if(!empty($endtime)){ echo $endtime;}else{echo "结束时间";}?>"/>
				</td>
				
			</tr>
			<tr>
				
			               <td style="text-align:center">省份：</td>
			               <td>
				               <select name="province_id" id="province_id" class="form-control" >
				                	<option value="">全部</option>
				                    <?php foreach($allprovince as $k=>$v){?>
				                	<option value="<?php echo $k;?>" <?php echo $province_id==$k?"selected":"";?>><?php echo $v;?></option>
				                    <?php }?>
				                </select>
			                </td>
				<td style="text-align:center">城市：</td>
			               <td>
				                <select name="city_id" id="city_id" class="form-control" >
				                	<option value="">全部</option>
				                    <?php
				                    if(isset($allcity[$province_id])){
										foreach($allcity[$province_id] as $k=>$v){
											$ck=$k==$city_id?'selected':'';
											echo '<option value="'.$k.'" '.$ck.'>'.$v.'</option>';
										}
									}
									?>
				               </select>
			                </td>
				<td>县区：</td>
			                <td>
				               <select name="area_id" id="area_id" class="form-control" >
				                	<option value="">全部</option>
				                    <?php
				                    if(isset($allarea[$city_id])){
										foreach($allarea[$city_id] as $k=>$v){
											$ck=$k==$area_id?'selected':'';
											echo '<option value="'.$k.'" '.$ck.'>'.$v.'</option>';
										}
									}
									?>
				                </select>
			                </td>
			                <td>
			                	16周岁以下
			                	<input type="checkbox" id="age" name="age" value="1" <?php if(!empty($age)) echo "checked";?>>
			                </td>
			                <td colspan=2>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp;
                    				<a href="<?php echo Config::get('app.url');?>/admin/orderList/{{$id}}" id="btn-dao" target="_blank">导出</a>
				</td>
			                </td>
			</tr>
		</table>
	</form>
	<script>
		$('#starttime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$('#endtime').datepicker({
			dateFormat:'yy-mm-dd'
			});
		$("#province_id").change(function(){
			//重置
			$("#city_id").empty();
			$("#area_id").empty();
			var _val=$(this).val();
			if(_val!=""){
				$.getJSON("/admin/getCity",{province_id:_val},function(data){
					var html="<option>全部</option>";
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
	</script>