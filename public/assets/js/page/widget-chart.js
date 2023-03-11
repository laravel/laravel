"use strict";

var sparkline_values = [10, 7, 4, 8, 5, 8, 6, 5, 2, 4, 7, 4, 9, 6, 5, 9];
var sparkline_values_chart = [2, 6, 4, 8, 3, 5, 2, 7];
var sparkline_values_bar = [10, 7, 4, 8, 5, 8, 6, 5, 2, 4, 7, 4, 9, 10, 7, 4, 8, 5, 8, 6, 5, 2, 4, 7, 4, 9, 8, 6, 5, 2, 4, 7, 4, 9, 10, 2, 4, 7, 4, 9, 7, 4, 8, 5, 8, 6, 5];

$('.sparkline-inline').sparkline(sparkline_values, {
  type: 'line',
  width: '100%',
  height: '32',
  lineWidth: 3,
  lineColor: 'rgba(87,75,144,.1)',
  fillColor: 'rgba(87,75,144,.25)',
  highlightSpotColor: 'rgba(87,75,144,.1)',
  highlightLineColor: 'rgba(87,75,144,.1)',
  spotRadius: 3,
});

$('.sparkline-line').sparkline(sparkline_values, {
  type: 'line',
  width: '100%',
  height: '32',
  lineWidth: 3,
  lineColor: 'rgba(63, 82, 227, .5)',
  fillColor: 'transparent',
  highlightSpotColor: 'rgba(63, 82, 227, .5)',
  highlightLineColor: 'rgba(63, 82, 227, .5)',
  spotRadius: 3,
});

$('.sparkline-line-chart').sparkline(sparkline_values_chart, {
  type: 'line',
  width: '100%',
  height: '32',
  lineWidth: 2,
  lineColor: 'rgba(63, 82, 227, .5)',
  fillColor: 'transparent',
  highlightSpotColor: 'rgba(63, 82, 227, .5)',
  highlightLineColor: 'rgba(63, 82, 227, .5)',
  spotRadius: 2,
});
$('.sparkline-line-chart2').sparkline(sparkline_values_chart, {
  type: "line",
  width: "100%",
  height: "100",
  lineWidth: 3,
  lineColor: "white",
  fillColor: "transparent",
  highlightSpotColor: "rgba(63,82,227,.1)",
  highlightLineColor: "rgba(63,82,227,.1)",
  spotRadius: 3
});

$(".sparkline-bar").sparkline(sparkline_values_bar, {
  type: "bar",
  width: "100%",
  height: "100",
  barColor: "white",
  barWidth: 2
});


var ctx = document.getElementById("myChart").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    datasets: [{
      label: 'Statistics',
      data: [460, 458, 330, 502, 430, 610, 488],
      borderWidth: 2,
      backgroundColor: 'rgba(145,141,197,.8)',
      borderWidth: 0,
      borderColor: 'transparent',
      pointBorderWidth: 0,
      pointRadius: 3.5,
      pointBackgroundColor: 'transparent',
      pointHoverBackgroundColor: 'rgba(63,82,227,.8)',
    }, {
      label: 'Statistics',
      data: [390, 600, 390, 280, 600, 430, 638],
      borderWidth: 2,
      backgroundColor: 'rgba(58,184,214,.7)',
      borderWidth: 0,
      borderColor: 'transparent',
      pointBorderWidth: 0,
      pointRadius: 3.5,
      pointBackgroundColor: 'transparent',
      pointHoverBackgroundColor: 'rgba(254,86,83,.8)',
    }]
  },
  options: {
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        gridLines: {
          drawBorder: false,
          color: '#f2f2f2',
        },
        ticks: {
          beginAtZero: true,
          stepSize: 200,
          fontColor: "#9aa0ac", // Font Color
          callback: function (value, index, values) {
            return value;
          }
        }
      }],
      xAxes: [{
        gridLines: {
          display: false,
          tickMarkLength: 15,
        },
        ticks: {
          fontColor: "#9aa0ac", // Font Color
        }
      }]
    },
  }
});

var ctx = document.getElementById("myChart2").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'bar',
  data: {
    labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    datasets: [{
      label: 'Statistics',
      data: [428, 354, 425, 528, 450, 632, 429],
      borderWidth: 2,
      backgroundColor: 'rgba(255,164,38,.9)',
      borderColor: 'rgba(255,164,38,.9)',
      borderWidth: 2.5,
      pointBackgroundColor: '#ffffff',
      pointRadius: 4
    }, {
      label: 'Statistics',
      data: [468, 387, 401, 574, 498, 568, 452],
      borderWidth: 2,
      backgroundColor: 'rgba(71,65,98,.9)',
      borderColor: 'transparent',
      borderWidth: 0,
      pointBackgroundColor: '#999',
      pointRadius: 4
    }]
  },
  options: {
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        gridLines: {
          drawBorder: false,
          color: '#f2f2f2',
        },
        ticks: {
          beginAtZero: true,
          stepSize: 150,
          fontColor: "#9aa0ac", // Font Color
        }
      }],
      xAxes: [{
        gridLines: {
          display: false
        },
        ticks: {
          fontColor: "#9aa0ac", // Font Color
        }
      }]
    },
  }
});

var ctx = document.getElementById("myChart3").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'line',
  data: {
    labels: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    datasets: [{
      label: 'Google',
      data: [290, 358, 220, 402, 440, 510, 688],
      borderWidth: 2,
      backgroundColor: 'transparent',
      borderColor: 'rgba(254,86,83,.7)',
      borderWidth: 2.5,
      pointBackgroundColor: 'transparent',
      pointBorderColor: 'transparent',
      pointRadius: 4
    },
    {
      label: 'Facebook',
      data: [450, 258, 390, 162, 650, 570, 438],
      borderWidth: 2,
      backgroundColor: 'transparent',
      borderColor: 'rgba(63,82,227,.8)',
      borderWidth: 0,
      pointBackgroundColor: 'transparent',
      pointBorderColor: 'transparent',
      pointRadius: 4
    },
    ]
  },
  options: {
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        gridLines: {
          drawBorder: false,
          color: '#f2f2f2',
        },
        ticks: {
          beginAtZero: true,
          stepSize: 200,
          fontColor: "#9aa0ac", // Font Color
        }
      }],
      xAxes: [{
        gridLines: {
          display: false
        },
        ticks: {
          fontColor: "#9aa0ac", // Font Color
        }
      }]
    },
  }
});

$('#visitorMap4').vectorMap(
  {
    map: 'world_en',
    backgroundColor: '#ffffff',
    borderColor: '#F5AE46',
    borderOpacity: .8,
    borderWidth: 1,
    hoverColor: '#000',
    hoverOpacity: .8,
    color: '#ddd',
    normalizeFunction: 'linear',
    selectedRegions: false,
    showTooltip: true,
    pins: {
      id: '<div class="jqvmap-circle"></div>',
      my: '<div class="jqvmap-circle"></div>',
      th: '<div class="jqvmap-circle"></div>',
      sy: '<div class="jqvmap-circle"></div>',
      eg: '<div class="jqvmap-circle"></div>',
      ae: '<div class="jqvmap-circle"></div>',
      nz: '<div class="jqvmap-circle"></div>',
      tl: '<div class="jqvmap-circle"></div>',
      ng: '<div class="jqvmap-circle"></div>',
      si: '<div class="jqvmap-circle"></div>',
      pa: '<div class="jqvmap-circle"></div>',
      au: '<div class="jqvmap-circle"></div>',
      ca: '<div class="jqvmap-circle"></div>',
      tr: '<div class="jqvmap-circle"></div>',
    },
  });

/* chart shadow */
var draw = Chart.controllers.line.prototype.draw;
Chart.controllers.lineShadow = Chart.controllers.line.extend({
  draw: function () {
    draw.apply(this, arguments);
    var ctx = this.chart.chart.ctx;
    var _stroke = ctx.stroke;
    ctx.stroke = function () {
      ctx.save();
      ctx.shadowColor = '#00000075';
      ctx.shadowBlur = 10;
      ctx.shadowOffsetX = 8;
      ctx.shadowOffsetY = 8;
      _stroke.apply(this, arguments)
      ctx.restore();
    }
  }
});

var balance_chart = document.getElementById("chart-1").getContext('2d');

var balance_chart_bg_color = balance_chart.createLinearGradient(0, 0, 0, 70);
balance_chart_bg_color.addColorStop(0, 'rgba(120, 107, 236, .2)');
balance_chart_bg_color.addColorStop(1, 'rgba(120, 107, 236, 0)');

var myChart = new Chart(balance_chart, {
  type: 'lineShadow',
  data: {
    labels: ['1990', '1991', '1992', '1993', '1994', '1995', '1996', '1997', '1998', '1999', '2000', '2001'],
    datasets: [{
      label: 'Balance',
      data: [50, 61, 80, 50, 72, 52, 60, 41, 30, 45, 70, 40],
      backgroundColor: balance_chart_bg_color,
      borderWidth: 3,
      borderColor: 'rgba(41, 192, 177, 1)',
      pointBorderWidth: 0,
      pointBorderColor: 'transparent',
      pointRadius: 3,
      pointBackgroundColor: 'transparent',
      pointHoverBackgroundColor: 'rgba(120, 107, 236,1)',
    }]
  },
  options: {
    layout: {
      padding: {
        bottom: -1,
        left: -1
      }
    },
    legend: {
      display: false
    },

    scales: {
      yAxes: [{
        gridLines: {
          display: false,
          drawBorder: false,
        },
        ticks: {
          beginAtZero: true,
          display: false,
          fontColor: "#9aa0ac", // Font Color
        }
      }],
      xAxes: [{
        gridLines: {
          drawBorder: false,
          display: false,
        },
        ticks: {
          display: false,
          fontColor: "#9aa0ac", // Font Color
        }
      }]
    },
  }
});

var sales_chart = document.getElementById("chart-2").getContext('2d');

var myChart = new Chart(sales_chart, {
  type: 'lineShadow',
  data: {
    labels: ['1990', '1991', '1992', '1993', '1994', '1995', '1996', '1997', '1998', '1999', '2000', '2001'],
    datasets: [{
      label: 'Sales',
      data: [70, 62, 44, 40, 21, 63, 82, 52, 50, 31, 70, 50],
      borderWidth: 2,
      backgroundColor: balance_chart_bg_color,
      borderWidth: 3,
      borderColor: 'rgba(156, 39, 176, 1)',
      pointBorderWidth: 0,
      pointBorderColor: 'transparent',
      pointRadius: 3,
      pointBackgroundColor: 'transparent',
      pointHoverBackgroundColor: 'rgba(120, 107, 236,1)',
    }]
  },
  options: {
    layout: {
      padding: {
        bottom: -1,
        left: -1
      }
    },
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        gridLines: {
          display: false,
          drawBorder: false,
        },
        ticks: {
          beginAtZero: true,
          display: false
        }
      }],
      xAxes: [{
        gridLines: {
          drawBorder: false,
          display: false,
        },
        ticks: {
          display: false
        }
      }]
    },
  }
});
var sales_chart = document.getElementById("chart-3").getContext('2d');

var myChart = new Chart(sales_chart, {
  type: 'lineShadow',
  data: {
    labels: ['1990', '1991', '1992', '1993', '1994', '1995', '1996', '1997', '1998', '1999', '2000', '2001'],
    datasets: [{
      label: 'Sales',
      data: [63, 82, 52, 50, 31, 70, 50, 70, 62, 44, 40, 21],
      borderWidth: 2,
      backgroundColor: balance_chart_bg_color,
      borderWidth: 3,
      borderColor: 'rgba(76, 175, 80, 1)',
      pointBorderWidth: 0,
      pointBorderColor: 'transparent',
      pointRadius: 3,
      pointBackgroundColor: 'transparent',
      pointHoverBackgroundColor: 'rgba(120, 107, 236,1)',
    }]
  },
  options: {
    layout: {
      padding: {
        bottom: -1,
        left: -1
      }
    },
    legend: {
      display: false
    },
    scales: {
      yAxes: [{
        gridLines: {
          display: false,
          drawBorder: false,
        },
        ticks: {
          beginAtZero: true,
          display: false
        }
      }],
      xAxes: [{
        gridLines: {
          drawBorder: false,
          display: false,
        },
        ticks: {
          display: false
        }
      }]
    },
  }
});

var ctx = document.getElementById("donutChart").getContext('2d');
var myChart = new Chart(ctx, {
  type: 'doughnut',
  data: {
    datasets: [{
      data: [
        30,
        50,
        20,
      ],
      backgroundColor: [
        '#191d21',
        '#63ed7a',
        '#ffa426',
      ],
      label: 'Dataset 1'
    }],
    labels: [
      'Black',
      'Green',
      'Yellow',
    ],
  },
  options: {
    responsive: true,
    legend: {
      position: 'bottom',
      display: false
    },
  }
});

var ctx = document.getElementById('line-chart3').getContext("2d");

var gradientStroke = ctx.createLinearGradient(0, 0, 700, 0);
gradientStroke.addColorStop(0, 'rgba(255, 204, 128, 1)');
gradientStroke.addColorStop(0.5, 'rgba(255, 152, 0, 1)');
gradientStroke.addColorStop(1, 'rgba(239, 108, 0, 1)');

var myChart = new Chart(ctx, {
  type: 'lineShadow',
  data: {
    labels: ["2010", "2011", "2012", "2013", "2014", "2015", "2016"],
    type: 'line',
    defaultFontFamily: 'Poppins',
    datasets: [{
      label: "Income",
      data: [0, 30, 10, 120, 50, 63, 10],
      borderColor: gradientStroke,
      pointBorderColor: gradientStroke,
      pointBackgroundColor: gradientStroke,
      pointHoverBackgroundColor: gradientStroke,
      pointHoverBorderColor: gradientStroke,
      pointBorderWidth: 10,
      pointHoverRadius: 6,
      pointHoverBorderWidth: 1,
      pointRadius: 1,
      fill: false,
      borderWidth: 4,
    }, {
      label: "Expense",
      data: [0, 50, 40, 80, 40, 79, 120],
      borderColor: gradientStroke,
      pointBorderColor: gradientStroke,
      pointBackgroundColor: gradientStroke,
      pointHoverBackgroundColor: gradientStroke,
      pointHoverBorderColor: gradientStroke,
      pointBorderWidth: 10,
      pointHoverRadius: 6,
      pointHoverBorderWidth: 1,
      pointRadius: 1,
      fill: false,
      borderWidth: 4,
    }]
  },


  options: {
    legend: {
      position: "bottom",
      display: false
    },
    scales: {
      yAxes: [{
        ticks: {
          fontColor: "rgba(0,0,0,0.5)",
          fontStyle: "bold",
          beginAtZero: true,
          maxTicksLimit: 5,
          padding: 20,
          fontColor: "#9aa0ac", // Font Color
        },
        gridLines: {
          drawTicks: false,
          display: false
        }

      }],
      xAxes: [{
        gridLines: {
          zeroLineColor: "transparent"
        },
        ticks: {
          padding: 20,
          fontColor: "rgba(0,0,0,0.5)",
          fontStyle: "bold",
          fontColor: "#9aa0ac", // Font Color
        }
      }]
    }
  }
});

// revenue chart 
var options = {
  chart: {
    height: 230,
    type: "line",
    shadow: {
      enabled: true,
      color: "#000",
      top: 18,
      left: 7,
      blur: 10,
      opacity: 1
    },
    toolbar: {
      show: false
    }
  },
  colors: ["#3dc7be", "#ffa117"],
  dataLabels: {
    enabled: true
  },
  stroke: {
    curve: "smooth"
  },
  series: [{
    name: "High - 2019",
    data: [5, 15, 14, 36, 32, 32]
  },
  {
    name: "Low - 2019",
    data: [7, 11, 30, 18, 25, 13]
  }
  ],
  grid: {
    borderColor: "#e7e7e7",
    row: {
      colors: ["#f3f3f3", "transparent"], // takes an array which will be repeated on columns
      opacity: 0.0
    }
  },
  markers: {
    size: 6
  },
  xaxis: {
    categories: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],

    labels: {
      style: {
        colors: "#9aa0ac"
      }
    }
  },
  yaxis: {
    title: {
      text: "Income"
    },
    labels: {
      style: {
        color: "#9aa0ac"
      }
    },
    min: 5,
    max: 40
  },
  legend: {
    position: "top",
    horizontalAlign: "right",
    floating: true,
    offsetY: -25,
    offsetX: -5
  }
};

var chart = new ApexCharts(document.querySelector("#revenue"), options);

chart.render();

