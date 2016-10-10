<form action="/admin/cost" method='get'>
		<table >
			<tr>
               <td>用户昵称:</td>
			   <td style="width:140px">
					<input type="text" name="username" class="form-control"  value="<?php if(!empty($username)) {echo $username;}?>">
				</td>

    			<td>提现类型:</td>
				<td style="width:140px">
					<select class="form-control" id="pay_type" name="pay_type">
					  	<option value="-1">不限</option>
                        <option value=0 <?php if($pay_type == 0 && $pay_type!='') echo 'selected'?>>提现申请中</option>
					  	<option value=1 <?php if($pay_type == 1) echo 'selected'?>>提现成功</option>
					</select>
				</td>
 </tr>
  <tr>
  <td>开始时间:</td>
				<td>
					<input type="text" id="starttime" name="starttime"  class="form-control"  value="<?php if(!empty($starttime)) {echo $starttime;} else {echo "";}?>"/>
				</td>

				<td>结束时间:</td>
				<td>
					<input type="text" id="endtime" class="form-control"  name="endtime" value="<?php if(!empty($endtime)){ echo $endtime;}else{echo "";}?>"/>
				</td>
				  <td> </td>
				 <td>
					<input class="search btn btn-mini btn-success" type="submit"  value='查询' />&nbsp;&nbsp;&nbsp; 
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