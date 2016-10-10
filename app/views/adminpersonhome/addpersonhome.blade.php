@extends('layouts.adIndex')
@section('title')
    个人主页列表管理
@stop
@section('crumbs')
    个人主页列表管理
@stop
@section('search')
@stop
@section('content')
            {{ Form::open(['url' => 'admin/addPersonHome','method'=>'post','enctype'=>'multipart/form-data']) }}
            	               {{ Form::label('name','列表名称',array('class'=>'awesome'));}}
            	               {{ Form::text('name','',array('class'=>'form-control' ,'style'=>'width:150px;display:inline'));}}
                	{{ Form::label('icon', '上传图标',array('class'=>'awesome'))}}
                	{{ Form::file('icon',['class'=>'form-control','style'=>'width:200px;display:inline'])}}
                	{{ Form::label('sort','排序',array('class'=>'awesome'));}}
                	{{ Form::text('sort',1,array('class'=>'form-control' ,'style'=>'width:100px;display:inline'));}}
                	{{ Form::label('flag','自己或他人',array('class'=>'awesome'));}}
                	{{ Form::select('flag',[0=>'自己',1=>'他人'],0 ,['class'=>"form-control",'style'=>'width:150px;display:inline']);}}
                	{{ Form::label('category','独立或者同级',array('class'=>'awesome'));}}
                	{{ Form::select('category',$category,0 ,['class'=>"form-control",'style'=>'width:150px;display:inline']);}}
            	               {{ Form::submit('添加',array('class'=>'search btn btn-mini btn-success' ,'style'=>'width:100px;display:inline'));}}
            {{ Form::close() }}
<script type="text/javascript">
            $('#flag').bind('click',function(data){
                        var flag = $('#flag').find("option:selected").val();
                        $.post('/admin/getPersonHomeCategory',{flag:flag},function(data){
                        var tselect = "";
                        $.each(data,function(n,value){
                                var str = "";
                                if(n == 0){
                                     str += "<option value='"+n+"' selected='selected'>"+value+"</option>";
                                 }else{
                                    str += "<option value='"+n+"'>"+value+"</option>";
                                 }
                                 tselect += str;
                        });
                        //清除原来的列表
                        $('#category').empty();
                        $('#category').append(tselect);
        },"json")
    });
    
</script>
@stop
