@extends('layouts.adIndex')
 
@section('title')
活跃信息详情
@stop
@section('search')
<form action="{{ url('/admin/Activelist') }}"   method="post" >
  <table>
    <tr>
     <td><a href="/admin/listActive">_</a>年份</td><td><select name="year" class="form-control" style="width:200px"> 
      <?php $now=date("Y",time());  for($i=2014;$i<=$now;$i++){?>
   
        <option value="<?=$i?>" <?php if($year==$i) echo 'selected' ?>><?=$i?></option>
      <?php }?> 
    </select></td>
        <td><input class="btn btn-primary"  type="submit" value="筛选"/></td>
        <td>        </td>
    </tr>
  </table>
    </form>
@stop
@section('crumbs')
活跃信息详情
@stop
@section('content')
<body>
 <script src="http://cdn.hcharts.cn/jquery/jquery-1.8.3.min.js"></script>
<script src="http://cdn.hcharts.cn/highcharts/highcharts.js"></script> 
 


<div id="container" style="min-width:800px;height:400px"></div>

<div id="container1" style="min-width:800px;height:400px"></div>

<div id="container2" style="min-width:800px;height:400px"></div>

<div id="container3" style="min-width:800px;height:400px"></div>
</body>


<script >
 $(function () { 
    $('#container').highcharts({                   //图表展示容器，与div的id保持一致
        chart: {
            type: 'column'   
                                  //指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '日活跃数 '  
              //指定图表标题
        },
        xAxis: {
            categories: <?php echo $time ?>   //指定x轴分组
        },
        yAxis: {
            title: {
                text: '人数 '                //指定y轴的标题
            
            }
        },
        series: [{                                 //指定数据列
            name: '日活跃数',                          //数据列名
            data: <?php echo $day_hot ?>                        //数据
        }
        // ,{                                 //指定数据列
        //     name: '全站人数',                          //数据列名
        //     data: <?php echo $alluser ?>                        //数据
        // }
        ]
    });


    $('#container1').highcharts({                   //图表展示容器，与div的id保持一致
        chart: {
            type: 'column'                         //指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '日活跃率'      //指定图表标题
        },
        xAxis: {
            categories: <?php echo $time ?>   //指定x轴分组
        },
        yAxis: {
            title: {
                text: '百分比'                  //指定y轴的标题
            }
        },
        series: [ {
            name: '日活跃率',
            data: <?php echo $day_per ?>
   
        }]
    });

    $('#container2').highcharts({                   //图表展示容器，与div的id保持一致
        chart: {
            type: 'column'                         //指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '周活跃数'      //指定图表标题
        },
        xAxis: {
            categories: <?php echo $time ?>   //指定x轴分组
        },
        yAxis: {
            title: {
                text: '人数'                  //指定y轴的标题
            }
        },
        series: [ {
            name: '周活跃数',
            data: <?php echo $week_hot ?>
   
        }]
    });

    $('#container3').highcharts({                   //图表展示容器，与div的id保持一致
        chart: {
            type: 'column'                         //指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '周活跃率'      //指定图表标题
        },
        xAxis: {
            categories: <?php echo $time ?>   //指定x轴分组
        },
        yAxis: {
            title: {
                text: '百分比'                  //指定y轴的标题
            }
        },
        series: [ {
            name: '周活跃率',
            data: <?php echo $week_per ?>
   
        }]
    });
});
</script>
@stop