@extends('layouts.adIndex')
@section('title')
	报名学员列表
@stop
@section('crumbs')
	报名学员列表
@stop
@section('search')
@include('adminclassactive.search')
@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>id</th>
			<th>用户id</th>
			<th>姓名</th>
			<th>昵称</th>
			<th>性别</th>
			<th>身份证号</th>
			<th>手机号</th>
			<th>申请时间</th>
			<th>参赛码</th>
			<th>培训班</th>
			<th>交费/未交费</th>
			<th>处理/未处理</th>
		</tr>
		@foreach($data['list'] as $item)
			<tr>
				<td>{{$item['id']}}</td>
				<td>{{$item['uid']}}</td>
				<td>{{$item['name']}}</td>
				<td>{{$item['nick']}}</td>
				<td>
					@if($item['gender']==0)
						女
					@else
						男
					@endif
				</td>
				<td>{{$item['card']}}</td>
				<td>{{$item['mobile']}}</td>
				<td>{{date('Y-m-d H:i:s',$item['addtime'])}}</td>
				<td>{{$item['invitationcode']}}</td>
				<td>
					@if(!empty($data['classList'][$item['competition_id']]))
						{{$data['classList'][$item['competition_id']]}}
					@else
						未知
					@endif
				</td>
				<td>
					@if($item['status'] == 0)
						<button class="pay operator btn btn-mini btn-danger" type="button" data-status='{{$item["status"]}}' value='{{$item["id"]}}'>未交费</button>
					@elseif($item['status'] == 1)
						<button class="pay operator btn btn-mini btn-success" type="button"  data-status='{{$item["status"]}}' value='{{$item["id"]}}'>已交费</button>
					@endif
				</td>
				<td>
					@if($item['deal_status'] == 0)
						<button class="deal_status operator btn btn-mini btn-danger" type="button" data-deal-status='{{$item["deal_status"]}}' value='{{$item["id"]}}'>未处理</button>
					@elseif($item['deal_status'] == 1)
						<button class="deal_status operator btn btn-mini btn-success" type="button" data-deal-status='{{$item["deal_status"]}}' value='{{$item["id"]}}'>已处理</button>
					@endif
				</td>
			</tr>
			<tr>
				<td colspan="12" style="padding-left: 30px">
					@if(!empty($data['all_province'][$item['province_id']]))
						省份：{{$data['all_province'][$item['province_id']]}}	
					@endif
					@if(!empty($data['all_city'][$item['province_id']][$item['city_id']]))
						城市：{{$data['all_city'][$item['province_id']][$item['city_id']]}}
					@endif
					@if(!empty($data['all_area'][$item['city_id']][$item['area_id']]))
						县区：{{$data['all_area'][$item['city_id']][$item['area_id']]}}
					@endif
					<br>
					@if(!empty($item['address']))
						地址：{{$item['address']}}<br/>
					@endif
					@if(!empty($item['company']))
						单位名称：{{$item['company']}}<br/>
					@endif
					@if(!empty($item['zip']))
						邮编：{{$item['zip']}}
					@endif
					@if(!empty($item['email']))
						邮箱：{{$item['email']}}<br/>
					@endif
					@if(!empty($item['birthday']))
						生日：{{date('Y-m-d',$item['birthday'])}}<br/>
					@endif
					@if(!empty($item['orderid']))
						订单号：{{$item['orderid']}}<br/>
					@endif
					@if(!empty($item['goods_id']))
						所购商品：{{$item['goods_id']}}<br/>
					@endif
				</td>
			</tr>
		@endforeach
	</table>
	{{$data['list']->appends($search)->links()}}
	<script>
		//交费
		$('.pay').bind('click',function(data){
				var id = $(this).val();
				var data_status = $(this).attr('data-status');
				$.get("/admin/dealClassActiveStatudent",{id:id,status:data_status,flag:0},function(data){
					if(data == 1){
						alert('修改成功');
						location.reload();
					}else{
						alert(data);
						return;
					}
				});
		});
		//处理
		$('.deal_status').bind('click',function(data){
			var id = $(this).val();
			var data_deal_status = $(this).attr('data-deal-status');
			$.get("/admin/dealClassActiveStatudent",{id:id,status:data_deal_status,flag:1},function(data){
				if(data == 1){
					alert('修改成功');
					location.reload();
				}else{
					alert(data);
					return;
				}
			});
		});
	</script>
@stop

