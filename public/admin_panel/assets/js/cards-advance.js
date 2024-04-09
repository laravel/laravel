/**
 * Advanced Cards
 */

'use strict';

(function () {
  let labelColor;
  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
  } else {
    labelColor = config.colors.textMuted;
  }

  // Sales Bar Chart
  // --------------------------------------------------------------------
  const salesBarChartEl = document.querySelector('#salesChart'),
    salesBarChartConfig = {
      chart: {
        height: 120,
        parentHeightOffset: 0,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          barHeight: '100%',
          columnWidth: '25px',
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadius: 5,
          distributed: true,
          colors: {
            backgroundBarColors: [
              config.colors_label.primary,
              config.colors_label.primary,
              config.colors_label.primary,
              config.colors_label.primary
            ],
            backgroundBarRadius: 5
          }
        }
      },
      grid: {
        show: false,
        padding: {
          top: -30,
          left: -12,
          bottom: 10
        }
      },
      colors: [config.colors.primary],
      dataLabels: {
        enabled: false
      },
      series: [
        {
          data: [60, 35, 25, 75, 15, 42, 85]
        }
      ],
      legend: {
        show: false
      },
      xaxis: {
        categories: ['S', 'M', 'T', 'W', 'T', 'F', 'S'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      },
      responsive: [
        {
          breakpoint: 1440,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '30%'
              }
            }
          }
        },
        {
          breakpoint: 1200,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '15%'
              }
            }
          }
        },
        {
          breakpoint: 768,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '12%'
              }
            }
          }
        },
        {
          breakpoint: 450,
          options: {
            plotOptions: {
              bar: {
                columnWidth: '19%'
              }
            }
          }
        }
      ]
    };
  if (typeof salesBarChartEl !== undefined && salesBarChartEl !== null) {
    const salesBarChart = new ApexCharts(salesBarChartEl, salesBarChartConfig);
    salesBarChart.render();
  }
})();
