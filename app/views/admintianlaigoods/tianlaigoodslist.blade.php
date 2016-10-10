@extends('layouts.adIndex')
@section('title')
	天籁商城商品列表
@stop
@section('crumbs')
	天籁商城商品列表
@stop
@section('search')
	@include('admintianlaigoods.search')
@stop
@section('content')
{{ Form::open(array('url' => '/admin/tianLaiGoodsList')) }}
<table class="table table-hover table-bordered ">
	<tr>
		<th>商品id</th>
		<th>商品名称</th>
		<th>商品价格</th>
		<th>折扣价格</th>
		<th>会员价格</th>
		<th>邮费</th>
		<th>鲜花兑换</th>
		<th>折扣鲜花</th>
		<th>会员鲜花</th>
		<th>鲜花邮费</th>
		<th>是否现货</th>
		<th>下架/上架</th>
		<th>更新</th>
	</tr>
	@if(!empty($data['list']))
		@foreach($data['list'] as $item)
		<tr>
			<td>{{$item['id']}}</td>	
			<td>{{$item['name']}}</td>	
			<td>{{$item['price']}}</td>	
			<td>{{$item['discount_price']}}</td>	
			<td>{{$item['member_price']}}</td>	
			<td>{{$item['postage_price']}}</td>	
			<td>{{$item['flower_price']}}</td>	
			<td>{{$item['discount_flower_price']}}</td>	
			<td>{{$item['member_flower_price']}}</td>	
			<td>{{$item['flower_postage_price']}}</td>	
			<td>
				@if(isset($data['promptgoods'][$item['promptgoods']]))
					{{$data['promptgoods'][$item['promptgoods']]}}
				@else
					未知
				@endif
			</td>
			<td>
				@if(!empty($item['isdel']))
					<button class="operator btn btn-mini btn-success" type="button" value="{{$item['isdel']}}" data-id="{{$item['id']}}">上架</button>
				@else
					<button class="operator btn btn-mini btn-danger" type="button" value="{{$item['isdel']}}" data-id="{{$item['id']}}">下架</button>
				@endif
				
			</td>
			<td><a href="/admin/updateTianLaiGoods/{{$item['id']}}" >更新</a></td>	
		</tr>
		<tr>
			<td colspan="13">
				商品分类:
				@if(!empty($data['category'][$item['flag']]))
					{{$data['category'][$item['flag']]}}
				@else
					无
				@endif
				<br/>
				@if(!empty($item['normal_section']))
					普通人购买分段赠送钻石:{{$item['normal_section']}}<br/>
				@endif
				@if(!empty($item['member_section']))
					会员购买商品分段赠送钻石:{{$item['member_section']}}<br/>
				@endif
				@if(!empty($item['crowdfunding']))
					众筹目标数量:{{$item['crowdfunding']}}<br/>
				@endif
				@if(!empty($item['crowdfundinged']))
					已经筹到的数量:{{$item['crowdfundinged']}}<br/>
				@endif
				@if(!empty($item['normal_price_section']))
					普通购买分段计费:{{$item['normal_price_section']}}<br/>
				@endif
				@if(!empty($item['member_price_section']))
					会员购买分段计费:{{$item['member_price_section']}}<br/>
				@endif
				@if(!empty($item['normal_flower_price_section']))
					普通分段鲜花计费:{{$item['normal_flower_price_section']}}<br/>
				@endif
				@if(!empty($item['member_flower_price_section']))
					会员分段鲜花计费:{{$item['member_flower_price_section']}}<br/>
				@endif
				@if(!empty($item['icon']))
					商品图标:<br/>{{ Form::image($url.$item['icon'],'image',['width'=>'120px','height'=>'50px'])}}
				@endif
				
			</td>
		</tr>
		@endforeach
	@endif
</table>
{{ $data['list']->appends($search)->links()  }}
{{ Form::close() }}
<script type="text/javascript">
	$('.operator').bind('click',function(){
		isdel = $(this).val();
		id = $(this).attr('data-id');
		if(isdel == null || isdel == undefined){
			alert('操作参数错误');
			return;
		}
		if(confirm('确定执行该操作吗?')){
			$.ajax({
				url:'/admin/publishOrDelTianLaiGoods/'+id,
				method:'POST',
				data:{id:id,isdel:isdel},
			}).done(function(data){
				if(data==-1){
					alert('操作失败，请重试');
					return;
				}else{
					alert('操作成功');
					window.location.reload();
				}
			});
		}else{
			return;
		}
	});
</script>
@stop

