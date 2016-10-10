@extends('layouts.adIndex') 
@section('title') 
添加群组 
@stop 
@section('crumbs') 
添加群组 
@stop 
@section('search') 
@stop 
@section('content')
<script src="http://www.weinidushi.com.cn/js/My97DatePicker/WdatePicker.js"></script>
<table class="table table-hover table-bordered ">
  <form action="{{ url('/admin/addGroup') }}" method='post' enctype="multipart/form-data">
    <table class="table table-hover table-bordered ">
      <tr>
        <td width="200px">选择班级</td>
        <td>
          <select name="class_id" id="c_id" class="form-control" style="width:200px">
            <optgroup label='普通班级'></optgroup>
            <?php foreach($common as $k=>$v){?>

              <option value="<?php echo $v['id'];?>" style="margin-left:20">
                <?php echo $v['name'];?>
              </option>
              <?php }?>
                <?php foreach ($college as $key => $value) {?>
                  <optgroup label='<?php echo $key ?>'></optgroup>
                  <?php foreach ($value as $k  => $v ) { ?>
                    <optgroup label='<?php echo $k  ?>' style="margin-left:20px"></optgroup>
                    <option value="<?php echo $v['id'];?>" style="margin-left:-40px">
                      <?php echo $v['name'];?>
                    </option>
                    <?php  } } ?>
          </select>
        </td>
      </tr>
      <tr>
        <td>房间名称</td>
        <td>
          <input class="form-control input-large" id="hx_name" name="name" type="text" style="width:300px;">
        </td>
      </tr>
      <tr>
        <td>房间图片</td>
        <td>
          <input type="file" name="file" id="file" />
        </td>
      </tr>
      </tr>
      <tr>
        <td>管理员uid</td>
        <td>
          <input class="form-control input-large" name="owner" type="text" style="width:100px;">
        </td>

        <tr>
          <td>人数上限</td>
          <td>
            <input class="form-control input-large" name="maxusers" type="text" value="2000" style="width:100px;">
          </td>
        </tr>
        <tr>
          <td>群组描述</td>
          <td>
            <textarea name="desc" style="width:400px; height:80px;"></textarea>
          </td>
        </tr>

        <tr text-align="center">
          <td colspan="2">
            <input class="btn btn-danger" type="submit" name="提交" />
          </td>
        </tr>
    </table>
  </form>
  <script language="javascript">
       function Trim(str)
         { 
             return str.replace(/(^\s*)|(\s*$)/g, ""); 
     }
    $("#c_id").change(function() {
      var id = $(this).val();
      var name = $(this).find("option[value=" + id +"]").text();
    
      $("#hx_name").val(Trim(name));
    });
    $("#c_id").change();
  </script>
  @stop