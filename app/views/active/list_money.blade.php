@extends('layouts.adIndex')
 
@section('title')
消费报表
@stop
@section('search')
<form action="{{ url('/admin/moneyActive') }}"   method="post" >
  <table>
    <tr>
      <td><a href="/admin/moneyList">_</a>年份</td><td><select name="year" class="form-control" style="width:200px"> 
 
      <?php $now=date("Y",time());  for($i=2014;$i<=$now;$i++){?>
   
        <option value="<?=$i?>" <?php if($year==$i) echo 'selected' ?>><?=$i?></option>
      <?php }?> 
  </select></td>
        <td><input class="btn btn-primary"  type="submit" value="筛选"/></td>
      
    </tr>
  </table>
    </form>
@stop
@section('crumbs')
消费报表
@stop
@section('content')
<body>
 <script src="http://cdn.hcharts.cn/jquery/jquery-1.8.3.min.js"></script>
<script src="http://cdn.hcharts.cn/highcharts/highcharts.js"></script> 
 


<div id="container" style="min-width:800px;height:400px"></div>

<div id="container1" style="min-width:800px;height:400px"></div>

<div id="container2" style="min-width:800px;height:400px"></div>

</body>


<script >
 $(function () { 
    $('#container').highcharts({                   //图表展示容器，与div的id保持一致
        chart: {
            type: 'column'   
                                  //指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '月消费人数 '  
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
            name: '月消费人数',                          //数据列名
            data: <?php echo $user_buy ?>                        //数据
        }
      
        ]
    });
$('#container1').highcharts({                   //图表展示容器，与div的id保持一致
        chart: {
            type: 'column'   
                                  //指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '月收入 '  
              //指定图表标题
        },
        xAxis: {
            categories: <?php echo $time ?>   //指定x轴分组
        },
        yAxis: {
            title: {
                text: '元 '                //指定y轴的标题
            
            }
        },
        series: [{                                 //指定数据列
            name: '月收入',                          //数据列名
            data: <?php echo $money ?>                        //数据
        }
      
        ]
    });
    $('#container2').highcharts({                   //图表展示容器，与div的id保持一致
        chart: {
            type: 'column'   
                                  //指定图表的类型，默认是折线图（line）
        },
        title: {
            text: '用户消费比 '  
              //指定图表标题
        },
        xAxis: {
            categories: <?php echo $time ?>   //指定x轴分组
        },
        yAxis: {
            title: {
                text: '% '                //指定y轴的标题
            
            }
        },
        series: [{                                 //指定数据列
            name: '用户消费比',                          //数据列名
            data: <?php echo $buy_per ?>                        //数据
        }
      
        ]
    });
});
</script>
@stop