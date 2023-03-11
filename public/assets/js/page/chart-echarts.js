$(function (e) {
    'use strict'

    /* Chart data*/
    var chartdata = [
        {
            name: 'sales',
            type: 'bar',
            data: [11, 14, 8, 16, 11, 13]
        },
        {
            name: 'profit',
            type: 'line',
            smooth: true,
            data: [10, 7, 17, 11, 15],
            symbolSize: 10,
        },
        {
            name: 'growth',
            type: 'bar',
            data: [10, 14, 10, 15, 9, 25]
        }
    ];

    /* Bar chart echartopt1*/
    var chart = document.getElementById('echart_bar_line');
    var barChart = echarts.init(chart);

    var option = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: ['2014', '2015', '2016', '2017', '2018'],
            axisLine: {
                lineStyle: {
                    color: '#eaeaea'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        },
        tooltip: {
            show: true,
            showContent: true,
            alwaysShowContent: false,
            triggerOn: 'mousemove',
            trigger: 'axis',
            axisPointer:
            {
                label: {
                    show: false,
                }
            }

        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: '#eaeaea'
                }
            },
            axisLine: {
                lineStyle: {
                    color: '#eaeaea'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        },
        series: chartdata,
        color: ['#9f78ff', '#fa626b', '#32cafe',]
    };

    barChart.setOption(option);


    var chartdata2 = [
        {
            name: 'sales',
            type: 'line',
            smooth: true,
            data: [15, 22, 14, 31, 17, 41],
            symbolSize: 10,
            color: ['#FF8D60']
        },
        {
            name: 'Profit',
            type: 'line',
            smooth: true,
            symbolSize: 10,
            size: 10,
            data: [8, 12, 28, 10, 10, 12],
            color: ['#009DA0']
        }
    ];

    var chart2 = document.getElementById('echart_line');
    var barChart2 = echarts.init(chart2);
    var option2 = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: ['2014', '2015', '2016', '2017', '2018'],
            axisLine: {
                lineStyle: {
                    color: '#eaeaea'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        },
        tooltip: {
            show: true,
            showContent: true,
            alwaysShowContent: false,
            triggerOn: 'mousemove',
            trigger: 'axis',
            axisPointer:
            {
                label: {
                    show: false,
                },
            }
        },
        yAxis: {
            splitLine: {
                lineStyle: {
                    color: 'none'
                }
            },
            axisLine: {
                lineStyle: {
                    color: '#eaeaea'
                }
            },
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        },
        series: chartdata2,
    };
    barChart2.setOption(option2);



    /* Bar chart */

    /* Chart data*/
    var chartdata = [
        {
            name: 'sales',
            type: 'bar',
            data: [13, 14, 10, 16, 11, 13]
        },

        {
            name: 'growth',
            type: 'bar',
            data: [10, 14, 10, 15, 9, 25]
        }
    ];


    var chart = document.getElementById('echart_bar');
    var barChart = echarts.init(chart);

    var option = {
        grid: {
            top: '6',
            right: '0',
            bottom: '17',
            left: '25',
        },
        xAxis: {
            data: ['2014', '2015', '2016', '2017', '2018'],

            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        },
        tooltip: {
            show: true,
            showContent: true,
            alwaysShowContent: false,
            triggerOn: 'mousemove',
            trigger: 'axis',
            axisPointer:
            {
                label: {
                    show: false,
                }
            }
        },
        yAxis: {
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        },
        series: chartdata,
        color: ['#9f78ff', '#32cafe']
    };

    barChart.setOption(option);


    /* Bar Graph */
    var chart = document.getElementById('echart_graph_line');
    var barChart = echarts.init(chart);

    barChart.setOption({
        tooltip: {
            trigger: "axis"
        },
        legend: {
            textStyle: { color: '#9aa0ac' },
            data: ["sales", "purchases"]
        },
        toolbox: {
            show: !1
        },
        calculable: !1,
        xAxis: [{
            type: "category",
            data: ["2000", "2001", "2002", "2003", "2004", "2005"],
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        }],
        yAxis: [{
            type: "value",
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        }],
        series: [{
            name: "sales",
            type: "bar",
            data: [22, 54, 37, 23, 25.6, 76],
            markPoint: {
                data: [{
                    type: "max",
                    name: "???"
                }, {
                    type: "min",
                    name: "???"
                }]
            },
            markLine: {
                data: [{
                    type: "average",
                    name: "???"
                }]
            }
        }, {
            name: "purchases",
            type: "bar",
            data: [35, 45, 47, 10, 35, 70],
            markPoint: {
                data: [{
                    name: "sales",
                    value: 182.2,
                    xAxis: 7,
                    yAxis: 183
                }, {
                    name: "purchases",
                    value: 2.3,
                    xAxis: 11,
                    yAxis: 3
                }]
            },
            markLine: {
                data: [{
                    type: "average",
                    name: "???"
                }]
            }
        }],
        color: ['#9f78ff', '#32cafe']

    });

    /* Pie Chart */
    var chart = document.getElementById('echart_pie');
    var barChart = echarts.init(chart);

    barChart.setOption({
        tooltip: {
            trigger: "item",
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            x: "center",
            y: "bottom",
            textStyle: { color: '#9aa0ac' },
            data: ["Direct Access", "E-mail Marketing", "Video Ads", "Search Engine"]
        },

        calculable: !0,
        series: [{
            name: "Chart Data",
            type: "pie",
            radius: "55%",
            center: ["50%", "48%"],
            data: [{
                value: 335,
                name: "Direct Access"
            }, {
                value: 310,
                name: "E-mail Marketing"
            }, {
                value: 135,
                name: "Video Ads"
            }, {
                value: 548,
                name: "Search Engine"
            }]
        }],
        color: ['#575B7A', '#DE725C', '#72BE81', '#50A5D8']
    });

    /* line chart */
    var chart = document.getElementById('echart_area_line');
    var lineChart = echarts.init(chart);

    lineChart.setOption({
        tooltip: {
            trigger: "axis"
        },
        legend: {
            textStyle: { color: '#9aa0ac' },
            data: ["Intent", "Pre-order", "Deal"]
        },
        calculable: !0,
        xAxis: [{
            type: "category",
            boundaryGap: !1,
            data: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        }],
        yAxis: [{
            type: "value",
            axisLabel: {
                fontSize: 10,
                color: '#9aa0ac'
            }
        }],
        series: [{
            name: "Deal",
            type: "line",
            smooth: !0,
            itemStyle: {
                normal: {
                    areaStyle: {
                        type: "default"
                    }
                }
            },
            data: [10, 12, 21, 54, 260, 830, 710]
        }, {
            name: "Pre-order",
            type: "line",
            smooth: !0,
            itemStyle: {
                normal: {
                    areaStyle: {
                        type: "default"
                    }
                }
            },
            data: [30, 182, 434, 791, 390, 30, 10]
        }, {
            name: "Intent",
            type: "line",
            smooth: !0,
            itemStyle: {
                normal: {
                    areaStyle: {
                        type: "default"
                    }
                }
            },
            data: [1320, 1132, 601, 234, 120, 90, 20]
        }],
        color: ['#9f78ff', '#fa626b', '#32cafe',]
    });

    /* Sonar Chart */
    var chart = document.getElementById('echart_sonar');
    var sonarChart = echarts.init(chart);

    sonarChart.setOption({
        tooltip: {
            trigger: "item"
        },
        legend: {
            orient: "vertical",
            x: "right",
            y: "bottom",
            textStyle: { color: '#9aa0ac' },
            data: ["Allocated Budget", "Actual Spending"]
        },
        polar: [{
            indicator: [{
                text: "Sales",
                max: 6e3
            }, {
                text: "Administration",
                max: 16e3
            }, {
                text: "Information Techology",
                max: 3e4
            }, {
                text: "Customer Support",
                max: 38e3
            }, {
                text: "Development",
                max: 52e3
            }, {
                text: "Marketing",
                max: 25e3
            }]
        }],
        calculable: !0,
        series: [{
            name: "Budget vs spending",
            type: "radar",
            data: [{
                value: [4300, 1e4, 28e3, 35e3, 5e4, 19e3],
                name: "Allocated Budget"
            }, {
                value: [5e3, 14e3, 28e3, 31e3, 42e3, 21e3],
                name: "Actual Spending"
            }]
        }]
    });

    /* Donut Chart */
    var chart = document.getElementById('echart_donut');
    var donutChart = echarts.init(chart);

    donutChart.setOption({
        tooltip: {
            trigger: "item",
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        calculable: !0,
        legend: {
            x: "center",
            y: "bottom",
            textStyle: { color: '#9aa0ac' },
            data: ["Direct Access", "E-mail Marketing", "Union Ad", "Video Ads", "Search Engine"]
        },
        series: [{
            name: "Access to the resource",
            type: "pie",
            radius: ["35%", "55%"],
            itemStyle: {
                normal: {
                    label: {
                        show: !0
                    },
                    labelLine: {
                        show: !0
                    }
                },
                emphasis: {
                    label: {
                        show: !0,
                        position: "center",
                        textStyle: {
                            fontSize: "14",
                            fontWeight: "normal"
                        }
                    }
                }
            },
            data: [{
                value: 335,
                name: "Direct Access"
            }, {
                value: 310,
                name: "E-mail Marketing"
            }, {
                value: 234,
                name: "Union Ad"
            }, {
                value: 135,
                name: "Video Ads"
            }, {
                value: 1548,
                name: "Search Engine"
            }]
        }]
    });

});

