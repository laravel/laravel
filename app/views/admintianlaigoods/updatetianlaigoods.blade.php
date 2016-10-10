@extends('layouts.adIndex')
@section('title')
	更新天籁商城商品
@stop
@section('crumbs')
	更新天籁商城商品
@stop
@section('content')
{{ Form::open(array('url' => 'admin/doUpdateTianLaiGoods','method'=>'post',"enctype"=>"multipart/form-data")) }}
{{ Form::token()}}
@if(count($errors) > 0)
	@foreach($errors->all() as $error)
		<div class="alert alert-danger" role="alert">{{$error}}</div>
	@endforeach
@endif
<table class="table table-striped">
<tr>
	<th width=150px>{{ Form::label('商品id','id',['class'=>'text-primary'])}}:</th>	
	<td colspan="3">
		{{ Form::text('disableed_id',$rs['id'],['class'=>'form-control','disabled'=>'disabled'])}}
		{{ Form::hidden('id',$rs['id'])}}
	</td>
</tr>
<tr>
	<th width=150px>{{ Form::label('category','商品分类',['class'=>'text-primary'])}}:</th>	
	<td colspan="3">{{ Form::select('category',$data['goodsCategory'],$rs['flag'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th>{{ Form::label('name','商品名称',['class'=>'text-primary','id'=>'forName'])}}:</th>	
	<td colspan="3">{{ Form::text('name',$rs['name'],['class'=>'form-control','onfocusout'=>"selfAlert(this)"])}}</td>	
</tr>
<tr>
	<th>{{ Form::label('type','商品单位',['class'=>'text-primary','id'=>'forType'])}}:</th>	
	<td colspan="3">{{ Form::select('type',$data['type'],$rs['type'],['class'=>'form-control','onfocusout'=>"selfAlert(this)"])}}</td>	
</tr>
<tr>
	<th>{{ Form::label('price','商品价格',['class'=>'text-primary','id'=>'forPrice'])}}:</th>	
	<td>{{ Form::text('price',$rs['price'],['class'=>'form-control','onfocusout'=>"selfAlert(this)"])}}</td>
	<th>{{ Form::label('discount_price','折扣价格',['class'=>'text-primary'])}}:</th>	
	<td>{{ Form::text('discount_price',$rs['discount_price'],['class'=>'form-control'])}}</td>
	<th>{{ Form::label('member_price','会员价:',['class'=>'text-primary'])}}</th>	
	<td>{{ Form::text('member_price',$rs['member_price'],['class'=>'form-control'])}}</td>
	<th>{{ Form::label('postage_price','邮费:',['class'=>'text-primary',])}}</th>	
	<td>{{ Form::text('postage_price',$rs['postage_price'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th colspan="2">{{ Form::label('normal_price_section','普通分段现金计费(填写格式(1-18|2-16) - 后表示每个的价格',['class'=>'text-primary'])}}</th>	
	<td colspan="8">{{ Form::text('normal_price_section',$rs['normal_price_section'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th colspan="2">{{ Form::label('member_price_section','会员分段现金计费(填写格式(1-18|2-15) - 后表示每个的价格',['class'=>'text-primary'])}}</th>	
	<td colspan="8">{{ Form::text('member_price_section',$rs['member_price_section'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th>{{ Form::label('flower_price','鲜花兑换',['class'=>'text-primary'])}}:</th>	
	<td>{{ Form::text('flower_price',$rs['flower_price'],['class'=>'form-control'])}}</td>
	<th>{{ Form::label('discount_flower_price','折扣鲜花',['class'=>'text-primary'])}}</th>	
	<td>{{ Form::text('discount_flower_price',$rs['discount_flower_price'],['class'=>'form-control'])}}</td>
	<th>{{ Form::label('member_flower_price','会员鲜花',['class'=>'text-primary'])}}</th>	
	<td>{{ Form::text('member_flower_price',$rs['member_flower_price'],['class'=>'form-control'])}}</td>

	<th>{{ Form::label('flower_postage_price','鲜花邮费',['class'=>'text-primary'])}}</th>	
	<td>{{ Form::text('flower_postage_price',$rs['flower_postage_price'],['class'=>'form-control'])}}</td>	
</tr>
<tr>
	<th colspan="2">{{ Form::label('normal_flower_price_section','普通分段鲜花计费(填写格式(1-18|2-16) - 后表示每个的价格',['class'=>'text-primary'])}}</th>	
	<td colspan="8">{{ Form::text('normal_flower_price_section',$rs['normal_flower_price_section'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th colspan="2">{{ Form::label('member_flower_price_section','会员分段鲜花计费(填写格式(1-18|2-15) - 后表示每个的价格',['class'=>'text-primary'])}}</th>	
	<td colspan="8">{{ Form::text('member_flower_price_section',$rs['member_flower_price_section'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th>{{ Form::label('promptgoods','是否现货',['class'=>'text-primary'])}}:</th>	
	<td>
		{{Form::label('promptgoods0','现货',['class'=>'text-primary'])}}
		{{Form::radio('promptgoods',0,$rs['promptgoods']==0 ? true : '',['id'=>'promptgoods0','class'=>'promptgoods'])}}
		{{Form::label('promptgoods1','无货',['class'=>'text-primary'])}}
		{{Form::radio('promptgoods',1,$rs['promptgoods'] == 1 ? true : '',['id'=>'promptgoods1','style'=>'margin-left:5px','class'=>'promptgoods'])}}
		{{Form::label('promptgoods2','众筹',['class'=>'text-primary'])}}
		{{Form::radio('promptgoods',2,$rs['promptgoods'] == 2 ? true : '',['id'=>'promptgoods2','style'=>'margin-left:5px','class'=>'promptgoods'])}}
	</td>
	<th>{{ Form::label('crowdfunding','众筹数量',['class'=>'text-primary'])}}:</th>	
	<td>{{ Form::text('crowdfunding',$rs['crowdfunding'] ? $rs['crowdfunding'] : '',['class'=>'form-control','disabled'=>'disabled','placeholder'=>"选择众筹并填写众筹数量"])}}</td>

</tr>
<tr>
	<th>{{ Form::label('diamond','普通送钻石',['class'=>'text-primary'])}}:</th>	
	<td>{{ Form::text('diamond',$rs['diamond'],['class'=>'form-control'])}}</td>

	<th width=120>{{ Form::label('member_diamond','会员送钻石',['class'=>'text-primary'])}}:</th>	
	<td colspan="2">{{ Form::text('member_diamond',$rs['member_diamond'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th colspan="2">{{ Form::label('normal_section','普通分段送钻石(填写格式(200-10|400-20)):',['class'=>'text-primary'])}}</th>
	<td colspan="6">{{Form::text('normal_section',$rs['normal_section'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th colspan="2">{{ Form::label('member_section','会员分段送钻石(填写格式(200-10|400-20)):',['class'=>'text-primary'])}}</th>
	<td colspan="6">{{Form::text('member_section',$rs['member_section'],['class'=>'form-control'])}}</td>
</tr>
<tr>
	<th>{{ Form::label('icon','商品图标',['class'=>'text-primary'])}}</th>
	<td colspan="3">
		{{ Form::file('icon')}}
		{{Form::image($rs['icon'],'商品图标',['title'=>'商品图标','style'=>'width:auto;height:160px;'])}}
	</td>
</tr>
<tr>
	<th>{{ Form::label('description','商品简介',['class'=>'text-primary'])}}:</th>	
	<td colspan="7">
		{{ Form::textarea('description',$rs['description'],['class'=>'form-control','rows'=>3,'onfocusout'=>"selfAlert(this)"])}}
		{{ Form::hidden('goodspic','',['id'=>'goodspic','style'=>'width:500px'])}}
	</td>	
</tr>
<tr>
            <th>{{ Form::label('des_detail','商品详情',['class'=>'text-primary'])}}:</th>  
            <td colspan="7">{{ Form::textarea('des_detail',$rs['des_detail'],['class'=>'form-control','rows'=>20])}}</td>  
</tr>
<tr>
	<th>{{ Form::label('images',"商品图片",['class'=>'text-primary'])}}<br/><span style="color:red">(点击上传顺序<br/>即为图片显示<br/>顺序,若要修改图片顺序，删除原来所有图片，重新上传):</span></th>	
             <td colspan="7">
                    {{ Form::file('inutdim1[]',['id'=>'input-dim-1','multiple'=>'multiple','class'=>'file-loading','accept'=>'image/*'])}}
            </td>
</tr>
<tr>
        <td></td>
        <td colspan="7">{{Form::submit('提交',['class'=>'btn btn-info']);}}</td>
</tr>
</table>
{{ Form::close() }}
<link href="/upload/css/fileinput.min.css" media="all" rel="stylesheet" type="text/css" />
<script src="/upload/js/plugins/canvas-to-blob.min.js" type="text/javascript"></script>
<script src="/upload/js/plugins/sortable.min.js" type="text/javascript"></script>
<script src="/upload/js/plugins/purify.min.js" type="text/javascript"></script>
<script src="/upload/js/fileinput.min.js"></script>
<script src="/upload/js/themes/fa/theme.js"></script>
<script src="/upload/js/locales/zh.js"></script>
<script>
//重新进入页面后刷新图片
$(function(){
	$('#goodspic').val('');
});

function selfAlert(o){
	val = $(o).val();
	if(val == null || val == 0){
		$(o).parent().addClass('has-error');
		$(o).focus();
	}else{
		$(o).parent().removeClass('has-error');
	}
}
$(document).on("ready",function(){
    $("#input-dim-1").fileinput({
        showCaption:true,
        showPreview:true,
        showRemove:false,
        showUpload:false,
        browseOnZoneClick:true,
        removeFromPreviewOnError:false,
        dropZoneEnabled:true,
        dropZoneTitle:'也可以将图片拖到此处',
        language:'zh',
        uploadAsync:true,
        uploadUrl: "/admin/uploadGoodsImage",
        allowedFileTypes:['image'],
        allowedFileExtensions: ["jpg", "png", "gif"],
        minImageWidth: 50,
        minImageHeight: 50,
        initialPreview:<?php echo $initialPreview;?>,
        initialPreviewConfig:<?php echo $initialPreviewConfig;?>,

    });

    $('#input-dim-1').on('fileuploaded', function(event, data, previewId, index) {
            var form = data.form,files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;
            id = previewId+'_'+response.id;
            tmp_id = $('#goodspic').val();
            tmp_id = tmp_id+'|'+id;
            $('#goodspic').val(tmp_id);
    }).on('filesuccessremove',function(event,id){
    	tmp_ids = $('#goodspic').val();
    	//去除前面的|号
    	pic_ids = tmp_ids.replace(/^\|/g, "");
    	arr = pic_ids.split('|');
    	last_str = '';
    	for (i in arr){
    		tmp_arr = arr[i].split('_');
    		if(id != tmp_arr[0]){
    			last_str += arr[i]+'|';
    		}
    	}
    	$('#goodspic').val(last_str);
    });
});

$('.promptgoods').each(function(){
	if($(this).is(":checked")){
		val = $(this).val();
		val = $(this).val();
		if(val == 2){
			$('#crowdfunding').removeAttr('disabled');
		}else{
			$('#crowdfunding').attr('disabled','disabled');
		}
	}
});
$(function(){
	$('.promptgoods').each(function(){
		$(this).click(function(){
			if($(this).is(":checked")){
				val = $(this).val();
				if(val == 2){
					$('#crowdfunding').removeAttr('disabled');
				}else{
					$('#crowdfunding').attr('disabled','disabled');
				}
			}
		});
	});
});

</script>
@stop

