@extends('layouts.adIndex')
@section('title')
	修改伴奏
@stop
@section('crumbs')
	修改伴奏
@stop
@section('content')
<form action="{{ url('/admin/updatePoemDo') }}" method="post" enctype="multipart/form-data">
<input type="hidden" name="id" value="{{$poem['id']}}" />
<table class="table table-hover table-bordered ">
    <tr>
        <td width="150px">伴奏名称</td>
        <td width="600px"><input type="text" class="form-control" id="name" name = "name" value="{{$poem['name']}}"></td>
    </tr>
    <tr>
        <td>伴奏首字母(大写必填)</td>
        <td><input type="text" class="form-control" id="allchar" name = "allchar" value="{{$poem['allchar']}}"></td>
    </tr>
    <tr>
        <td>诗拼音首字母(大写)</td>
        <td><input type="text" class="form-control" id="spelling" name = "spelling" value="{{$poem['spelling']}}"></td>
    </tr>
    <tr>
        <td>分类</td>
        <td>
        @foreach($alltype as $key=>$item)
            @if($key%8 == 0 && $key != 0)
                <br/>
            @endif
            <input name="category[]" id="cate_<?php echo $item["id"];?>" class="category" type="checkbox" value='{{$item['id']}}' <?php if(in_array($item['id'],$type)){echo "checked";}?> /><label for="cate_<?php echo $item["id"];?>" style="font-weight:100;">{{$item['category']}}</label>
            &nbsp;&nbsp;&nbsp;&nbsp;
        @endforeach
        </td>
    </tr>
    <tr>
        <td>诗别名</td>
        <td><input type="text" class="form-control" id="aliasname" name = "aliasname" value="{{$poem['aliasname']}}"></td>
    </tr>
    <tr>
        <td>读者名字</td>
        <td>
        	<input type="hidden" class="form-control" id="old_readername" name = "old_readername" value="{{$poem['readername']}}">
        	<input type="text" class="form-control" id="readername" name = "readername" value="{{$poem['readername']}}">
        </td>
    </tr>
    <tr>
        <td>读者首字母(大写必填)</td>
        <td><input type="text" class="form-control" id="readerallchar" name = "readerallchar" value="{{$poem['readerallchar']}}"></td>
    </tr>
    
    <tr>
        <td>写者名字(必填)</td>
        <td>
        	<input type="hidden" class="form-control" id="old_writername" name = "old_writername" value="{{$poem['writername']}}">
        	<input type="text" class="form-control" id="writername" name = "writername" value="{{$poem['writername']}}">
        </td>
    </tr>
    <tr>
        <td>写者首字母(大写必填)</td>
        <td><input type="text" class="form-control" id="writerallchar" name = "writerallchar" value="{{$poem['writerallchar']}}"></td>
    </tr>
    <tr>
        <td>伴奏写者分类</td>
        <td>
            <input type="radio" name="sex" id = "sex" value="0" <?php echo $poem['sex']==0?'checked':'';?> />都可以
            <input type="radio" name="sex" id = "sex" value="1" <?php echo $poem['sex']==1?'checked':'';?> />男读
            <input type="radio" name="sex" id = "sex" value="2" <?php echo $poem['sex']==2?'checked':'';?> />女读
        </td>
    </tr>
    <tr>
        <td>伴奏时长</td>
        <td><input type="text" class="form-control" id="duration" name = "duration" value="{{$poem['duration']}}"></td>
    </tr>
    <tr>
        <td>伴奏url</td>
        <td><input type="text" class="form-control" id="burl" name = "burl" value="{{$poem['burl']}}"></td>
    </tr>
    <tr>
        <td>原唱url</td>
        <td><input type="text" class="form-control" id="yurl" name = "yurl" value="{{$poem['yurl']}}"></td>
    </tr>
    <tr>
        <td>歌词url</td>
        <td><input type="text" class="form-control" id="lyricurl" name = "lyricurl" value="{{$poem['lyricurl']}}"></td>
    </tr>
    
    <tr text-align="center">
        <td  colspan="2"><input id="sub" class = "btn btn-danger" type="submit" value="更新" name="sub" /></td>
    </tr>
</table>
</form>	
@stop

