/**
 * eCommerce Dashboard
 */

'use strict';
(function () {
  let cardColor, headingColor, labelColor, legendColor, borderColor, shadeColor;

  if (isDarkStyle) {
    cardColor = config.colors_dark.cardColor;
    headingColor = config.colors_dark.headingColor;
    labelColor = config.colors_dark.textMuted;
    legendColor = config.colors_dark.bodyColor;
    borderColor = config.colors_dark.borderColor;
    shadeColor = 'dark';
  } else {
    cardColor = config.colors.white;
    headingColor = config.colors.headingColor;
    labelColor = config.colors.textMuted;
    legendColor = config.colors.bodyColor;
    borderColor = config.colors.borderColor;
    shadeColor = 'light';
  }

  // Visits - Multi Radial Bar Chart
  // --------------------------------------------------------------------
  const visitsRadialChartEl = document.querySelector('#visitsRadialChart'),
    visitsRadialChartConfig = {
      chart: {
        height: 270,
        type: 'radialBar'
      },
      colors: [config.colors.primary, config.colors.danger, config.colors.warning],
      series: [75, 80, 85],
      plotOptions: {
        radialBar: {
          offsetY: -10,
          hollow: {
            size: '45%'
          },
          track: {
            margin: 10,
            background: cardColor
          },
          dataLabels: {
            name: {
              fontSize: '15px',
              colors: [legendColor],
              fontFamily: 'IBM Plex Sans',
              offsetY: 25
            },
            value: {
              fontSize: '2rem',
              fontFamily: 'Rubik',
              fontWeight: 500,
              color: headingColor,
              offsetY: -15
            },
            total: {
              show: true,
              label: 'Total Visits',
              fontSize: '15px',
              fontWeight: 400,
              fontFamily: 'IBM Plex Sans',
              color: legendColor
            }
          }
        }
      },
      grid: {
        padding: {
          top: -10,
          bottom: -10
        }
      },
      stroke: {
        lineCap: 'round'
      },
      labels: ['Target', 'Mart', 'Ebay'],
      legend: {
        show: true,
        position: 'bottom',
        horizontalAlign: 'center',
        labels: {
          colors: legendColor,
          useSeriesColors: false
        },
        itemMargin: {
          horizontal: 15
        },
        markers: {
          width: 10,
          height: 10,
          offsetX: -3
        }
      }
    };

  if (typeof visitsRadialChartEl !== undefined && visitsRadialChartEl !== null) {
    const visitsRadialChart = new ApexCharts(visitsRadialChartEl, visitsRadialChartConfig);
    visitsRadialChart.render();
  }

  // Revenue Growth - Bar Chart
  // --------------------------------------------------------------------
  const revenueGrowthChartEl = document.querySelector('#revenueGrowthChart'),
    revenueGrowthChartConfig = {
      chart: {
        height: 90,
        type: 'bar',
        stacked: true,
        toolbar: {
          show: false
        }
      },
      grid: {
        show: false,
        padding: {
          left: 0,
          right: 0,
          top: -20,
          bottom: -20
        }
      },
      plotOptions: {
        bar: {
          horizontal: false,
          columnWidth: '20%',
          borderRadius: 2,
          startingShape: 'rounded',
          endingShape: 'flat'
        }
      },
      legend: {
        show: false
      },
      dataLabels: {
        enabled: false
      },
      colors: [config.colors.info, config.colors_label.secondary],
      series: [
        {
          name: '2020',
          data: [80, 60, 125, 40, 50, 30, 70, 80, 100, 40, 80, 60, 120, 75, 25, 135, 65]
        },
        {
          name: '2021',
          data: [50, 65, 40, 100, 30, 30, 80, 20, 50, 45, 30, 90, 70, 40, 50, 40, 60]
        }
      ],
      xaxis: {
        categories: ['10', '', '', '', '', '', '', '', '15', '', '', '', '', '', '', '', '20'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          style: {
            colors: labelColor
          },
          offsetY: -5
        }
      },
      yaxis: {
        show: false,
        floating: true
      },
      tooltip: {
        x: {
          show: false
        }
      }
    };
  if (typeof revenueGrowthChartEl !== undefined && revenueGrowthChartEl !== null) {
    const revenueGrowthChart = new ApexCharts(revenueGrowthChartEl, revenueGrowthChartConfig);
    revenueGrowthChart.render();
  }

  // Order Summary - Area Chart
  // --------------------------------------------------------------------
  const orderSummaryEl = document.querySelector('#orderSummaryChart'),
    orderSummaryConfig = {
      chart: {
        height: 230,
        type: 'area',
        toolbar: false,
        dropShadow: {
          enabled: true,
          top: 18,
          left: 2,
          blur: 3,
          color: config.colors.primary,
          opacity: 0.15
        }
      },
      markers: {
        size: 6,
        colors: 'transparent',
        strokeColors: 'transparent',
        strokeWidth: 4,
        discrete: [
          {
            fillColor: cardColor,
            seriesIndex: 0,
            dataPointIndex: 9,
            strokeColor: config.colors.primary,
            strokeWidth: 4,
            size: 6,
            radius: 2
          }
        ],
        hover: {
          size: 7
        }
      },
      series: [
        {
          data: [15, 18, 13, 19, 16, 31, 18, 26, 23, 39]
        }
      ],
      dataLabels: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        lineCap: 'round'
      },
      colors: [config.colors.primary],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          shadeIntensity: 0.8,
          opacityFrom: 0.7,
          opacityTo: 0.25,
          stops: [0, 95, 100]
        }
      },
      grid: {
        show: true,
        borderColor: borderColor,
        padding: {
          top: -15,
          bottom: -10,
          left: 15,
          right: 10
        }
      },
      xaxis: {
        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
        labels: {
          offsetX: 0,
          style: {
            colors: labelColor,
            fontSize: '13px'
          }
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        lines: {
          show: false
        }
      },
      yaxis: {
        labels: {
          offsetX: 7,
          formatter: function (val) {
            return '$' + val;
          },
          style: {
            fontSize: '13px',
            colors: labelColor
          }
        },
        min: 0,
        max: 40,
        tickAmount: 4
      }
    };
  if (typeof orderSummaryEl !== undefined && orderSummaryEl !== null) {
    const orderSummary = new ApexCharts(orderSummaryEl, orderSummaryConfig);
    orderSummary.render();
  }

  // Marketing Campaign - Donut Chart 1
  // --------------------------------------------------------------------
  const marketingCampaignChart1El = document.querySelector('#marketingCampaignChart1'),
    marketingCampaignChart1Config = {
      chart: {
        height: 55,
        width: 55,
        fontFamily: 'IBM Plex Sans',
        type: 'donut'
      },
      dataLabels: {
        enabled: false
      },
      grid: {
        padding: {
          top: -5,
          bottom: -5,
          left: -2,
          right: 0
        }
      },
      series: [60, 45, 60],
      stroke: {
        width: 3,
        lineCap: 'round',
        colors: [cardColor]
      },
      colors: [config.colors.primary, config.colors.warning, config.colors.success],
      plotOptions: {
        pie: {
          donut: {
            size: '65%',
            labels: {
              show: false,
              value: {
                show: false
              },
              total: {
                show: false
              }
            }
          }
        }
      },
      legend: {
        show: false
      },
      states: {
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };

  if (typeof marketingCampaignChart1El !== undefined && marketingCampaignChart1El !== null) {
    const marketingCampaignChart1 = new ApexCharts(marketingCampaignChart1El, marketingCampaignChart1Config);
    marketingCampaignChart1.render();
  }

  // Marketing Campaign - Donut Chart 2
  // --------------------------------------------------------------------
  const marketingCampaignChart2El = document.querySelector('#marketingCampaignChart2'),
    marketingCampaignChart2Config = {
      chart: {
        height: 55,
        width: 55,
        fontFamily: 'IBM Plex Sans',
        type: 'donut'
      },
      dataLabels: {
        enabled: false
      },
      grid: {
        padding: {
          top: -5,
          bottom: -5,
          left: -2,
          right: 0
        }
      },
      series: [60, 30, 30],
      stroke: {
        width: 3,
        lineCap: 'round',
        colors: [cardColor]
      },
      colors: [config.colors.danger, config.colors.secondary, config.colors.primary],
      plotOptions: {
        pie: {
          donut: {
            size: '65%',
            labels: {
              show: false,
              value: {
                show: false
              },
              total: {
                show: false
              }
            }
          }
        }
      },
      legend: {
        show: false
      },
      states: {
        active: {
          filter: {
            type: 'none'
          }
        }
      }
    };

  if (typeof marketingCampaignChart2El !== undefined && marketingCampaignChart2El !== null) {
    const marketingCampaignChart2 = new ApexCharts(marketingCampaignChart2El, marketingCampaignChart2Config);
    marketingCampaignChart2.render();
  }
})();
