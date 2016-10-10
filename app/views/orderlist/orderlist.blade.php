@extends('layouts.adIndex')
@section('title')
	订单列表
@stop
@section('search')
	@include('orderlist.search')
<script language="javascript">
$("#btn-dao").click(function(){
	var status=$("#status").val();
	var pay_type=$("#pay_type").val();
	var plat_from=$("#plat_from").val();
	var goods_id=$("#goods_id").val();
	var starttime = $('#starttime').val();
	var endtime = $('#endtime').val();
	var age = <?php echo $age;?>;
	var url="<?php echo Config::get('app.url');?>/admin/orderList/{{$id}}";
	var url_str="?status="+status;
	var province_id = $('#province_id').val();
	var city_id = $('#city_id').val();
	var area_id = $('#area_id').val();
	url_str+="&pay_type="+pay_type;
	url_str+="&plat_from="+plat_from;
	url_str+="&goods_id="+goods_id;
	url_str+="&starttime="+starttime;
	url_str+="&endtime="+endtime;
	url_str +="&export_flag=1";
	url_str +="&province_id="+province_id;
	url_str +="&city_id="+city_id;
	url_str += "&area_id="+area_id;
	url_str += "&age="+age;

	$(this).attr("href",url+url_str);
	return true;
	
});
</script>

@stop
@section('crumbs')
	订单列表 <span style="color:red;margin-left:20px" >收入金额:{{$total_money}}</span> 
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>订单号</th>
			<th>用户id<i class="icon-search"></i></th>
			<th>用户昵称</th>
			<th>电话</th>
            <th>性别</th>
			<th>商品id</th>
			<th>价格</th>
			<th>数量</th>
			<th>总价</th>
			<th>支付类型</th>
			<th>说明</th>
			<th>支付状态</th>
			<th>支付时间</th>
			<th>修改时间</th>
			<th>平台类型</th>
		</tr>
		@foreach ($orderlist as $item)
		<tr>
			<td>{{$item['id']}}</td>
			<td>{{$item['orderid']}}</td>
			<td>{{$item['uid']}}</td>
			<td>
				@if(isset($user_info[$item['uid']]))
					{{$user_info[$item['uid']]['nick']}}
				@endif
			</td>
			<td>
				@if(!empty($com_user_info[$item['uid']]['mobile']))
					{{$com_user_info[$item['uid']]['mobile']}}
				@endif
			</td>
		            	<td>
		           	@if(isset($user_info[$item['uid']]) && $user_info[$item['uid']]['gender'] ==1 )
		               	男
		            	@else
		               	女
		            	@endif
		            </td>
			<td>{{$all_goods[$item['goods_id']]}}</td>
			<td>{{$item['price']}}</td>
			<td>{{$item['num']}}</td>
			<td>{{$item['total_price']}}</td>
			@if($item['pay_type'] == 1)
				<td>银联</td>
			@elseif($item['pay_type'] == 2)
				<td>支付宝</td>
			@elseif($item['pay_type'] == 3)
				<td>支付宝网页</td>
			@elseif($item['pay_type'] == 4)
				<td>财付通(微信)</td>
			@endif
			<td>{{$item['description']}}</td>
			@if($item['status'] == 2)
				<td>成功</td>
			@else
				<td>失败</td>
			@endif
			<td>{{date('Y/m/d H:i',$item['addtime'])}}</td>
			<td>{{date('Y/m/d H:i',$item['updatetime'])}}</td>
			@if($item['plat_from'] == 0)
				<td>IOS</td>
			@else
				<td>安卓</td>
			@endif
		</tr>
		@if(!empty($com_user_info))
		<tr >
			<td colspan="15" style="padding:15px 50px">
			<?php 
				$self_province_id = !empty($com_user_info[$item['uid']]['province_id']) ? $com_user_info[$item['uid']]['province_id'] : 0;
				$self_city_id = !empty($com_user_info[$item['uid']]['city_id'])  ? $com_user_info[$item['uid']]['city_id'] : 0;
				$self_area_id = !empty($com_user_info[$item['uid']]['area_id']) ? $com_user_info[$item['uid']]['area_id'] : 0;
			?>
				@if(!empty($com_user_info[$item['uid']]['card']))
				身份证号:{{$com_user_info[$item['uid']]['card']}} 
				<?php
					$tmp_age = substr($com_user_info[$item['uid']]['card'], 6,8);
					$tmp_age = time()-strtotime($tmp_age);
					$tmp_age = floor($tmp_age/(3600*24*365));
				?>
				<span style="margin-left:20px;">年龄:{{$tmp_age}}</span>
				@endif
				@if(!empty($allprovince[$self_province_id]))
				<span style="margin-left:20px;">省份：</span>{{$allprovince[$self_province_id]}}
				@endif

				@if(!empty($allcity[$self_province_id][$self_city_id]))
				<span style="margin-left:20px;">城市：</span>{{$allcity[$self_province_id][$self_city_id]}} 
				@endif

				@if(!empty($allarea[$self_city_id][$self_area_id]))
				<span style="margin-left:20px;">县区：</span>{{$allarea[$self_city_id][$self_area_id]}}
				@endif

				@if(!empty($com_user_info[$item['uid']]['company']))
				<span style="margin-left:20px;">单位名称：{{$com_user_info[$item['uid']]['company']}}</span> <br/>
				@endif

				@if(!empty($com_user_info[$item['uid']]['address']))
				地址： {{$com_user_info[$item['uid']]['address']}} 
				@endif
				@if(!empty($com_user_info[$item['uid']]['zip']))
				邮编：{{$com_user_info[$item['uid']]['zip']}}
				@endif
				@if(!empty($com_user_info[$item['uid']]['email']))
				邮箱：{{$com_user_info[$item['uid']]['email']}}<br>
				@endif

				@if(!empty($com_user_info[$item['uid']]['cause']))
					入会理由:{{$com_user_info[$item['uid']]['cause']}}
				@endif
			</td>
		</tr>
		@endif
		@endforeach
	</table>
	{{ $orderlist->appends(array('status'=>$status,'pay_type'=>$pay_type,'plat_from'=>$plat_from,'goods_id'=>$goods_id,'starttime'=>$starttime,'endtime'=>$endtime,'province_id'=>$province_id,'city_id'=>$city_id,'area_id'=>$area_id,'age'=>$age))->links()  }}
    <ul class="pagination"><li><a href="javascript:;">总共 <em style="color:red;">{{$total}}</em> 条记录</a></li></ul>

@stop

