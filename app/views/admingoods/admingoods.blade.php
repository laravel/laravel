@extends('layouts.adIndex')
@section('title')
	商品列表
@stop
@section('crumbs')
	商品列表
@stop
@section('search')
{{ Form::open(array('url' => '/admin/getGoodsList','method'=>'post')) }}
    {{Form::label('goods_category','商品类型:');}}
    {{Form::select("good_category_flag",$goods_category,$search['goods_category_flag'],array('class'=>'form-control' ,'style'=>'width:200px;display:inline'))}}
    {{Form::submit('搜索',array('class'=>"btn btn-default"));}}
{{ Form::close() }}

@stop
@section('content')
	<table class="table table-hover table-bordered ">
		<tr>
			<th>商品id</th>
			<th>商品名称</th>
			<th>商品描述</th>
			<th>商品价格</th>
			<th>更新</th>			
		</tr>
		@foreach($list as $item)
			<tr>
				<td>{{$item['id']}}</td>
				<td><?php echo $item["name"];?></td>
				<td><?php echo $item["description"];?></td>
				<td><?php echo $item["price"];?></td>
				<td><a class="btn btn-mini btn-success" href="/admin/updateGoods/{{$item['id']}}" target="_blank" >更新</a></td>
			</tr>
			@if ($item['icon'])
			<tr>
				<td colspan="13">
					<img src="{{$item['icon']}}">
				</td>
			</tr>
			@endif
		@endforeach
	</table>
<script type="text/javascript">
$('.operator').bind('focusout',function(){
	//操作类型 1 修改商品名称 2,修改商品描述 3修改价格
	var data_type = $(this).attr('data-type');
	var id = $(this).attr('data-id');
	var val = $(this).val();
	var token = $('input[name=_token]').val();
	if(data_type == null || id == null || val == null){
		alert('修改错误，请重试');
		return;
	}
	$.post('/admin/modifyGoodInfo',{data_type:data_type,id:id,val:val,'_token':token},function(data){
		if(data == 1){
			alert('修改成功');
			location.reload();
		}else if(data ==2){
			return;
		}else{
			alert(data);
			return;
		}
	});
});
</script>

@stop

