/**
 * Statistics Cards
 */

'use strict';
(function () {
  let shadeColor, borderColor, labelColor;

  if (isDarkStyle) {
    labelColor = config.colors_dark.textMuted;
    borderColor = config.colors_dark.borderColor;
    shadeColor = 'dark';
  } else {
    labelColor = config.colors.textMuted;
    borderColor = config.colors.borderColor;
    shadeColor = '';
  }

  // Conversion - Gradient Line Chart
  // --------------------------------------------------------------------
  const conversationChartEl = document.querySelector('#conversationChart'),
    conversationChartConfig = {
      series: [
        {
          data: [50, 100, 0, 60, 20, 30]
        }
      ],
      chart: {
        height: 40,
        type: 'line',
        zoom: {
          enabled: false
        },
        sparkline: {
          enabled: true
        },
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      tooltip: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      grid: {
        show: false,
        padding: {
          top: 5,
          left: 10,
          right: 10,
          bottom: 5
        }
      },
      colors: [config.colors.primary],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          type: 'horizontal',
          gradientToColors: undefined,
          opacityFrom: 0,
          opacityTo: 0.9,
          stops: [0, 30, 70, 100]
        }
      },
      xaxis: {
        labels: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof conversationChartEl !== undefined && conversationChartEl !== null) {
    const conversationChart = new ApexCharts(conversationChartEl, conversationChartConfig);
    conversationChart.render();
  }

  // Income - Gradient Line Chart
  // --------------------------------------------------------------------
  const incomeChartEl = document.querySelector('#incomeChart'),
    incomeChartConfig = {
      series: [
        {
          data: [40, 70, 38, 90, 40, 65]
        }
      ],
      chart: {
        height: 40,
        type: 'line',
        zoom: {
          enabled: false
        },
        sparkline: {
          enabled: true
        },
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      tooltip: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      grid: {
        show: false,
        padding: {
          top: 10,
          left: 10,
          right: 10,
          bottom: 0
        }
      },
      colors: [config.colors.warning],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          type: 'horizontal',
          gradientToColors: undefined,
          opacityFrom: 0,
          opacityTo: 0.9,
          stops: [0, 30, 70, 100]
        }
      },
      xaxis: {
        labels: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof incomeChartEl !== undefined && incomeChartEl !== null) {
    const incomeChart = new ApexCharts(incomeChartEl, incomeChartConfig);
    incomeChart.render();
  }

  // Profit - Gradient Line Chart
  // --------------------------------------------------------------------
  const profitChartEl = document.querySelector('#profitChart'),
    profitChartConfig = {
      series: [
        {
          data: [50, 80, 10, 82, 52, 95]
        }
      ],
      chart: {
        height: 40,
        type: 'line',
        zoom: {
          enabled: false
        },
        sparkline: {
          enabled: true
        },
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      tooltip: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      grid: {
        show: false,
        padding: {
          top: 10,
          left: 10,
          right: 10,
          bottom: 0
        }
      },
      colors: [config.colors.success],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          type: 'horizontal',
          gradientToColors: undefined,
          opacityFrom: 0,
          opacityTo: 0.9,
          stops: [0, 30, 70, 100]
        }
      },
      xaxis: {
        labels: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof profitChartEl !== undefined && profitChartEl !== null) {
    const profitChart = new ApexCharts(profitChartEl, profitChartConfig);
    profitChart.render();
  }

  // Expenses - Gradient Line Chart
  // --------------------------------------------------------------------
  const expensesLineChartEl = document.querySelector('#expensesLineChart'),
    expensesLineChartConfig = {
      series: [
        {
          data: [80, 40, 85, 5, 80, 35]
        }
      ],
      chart: {
        height: 40,
        type: 'line',
        zoom: {
          enabled: false
        },
        sparkline: {
          enabled: true
        },
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      tooltip: {
        enabled: false
      },
      stroke: {
        curve: 'smooth',
        width: 3
      },
      grid: {
        show: false,
        padding: {
          top: 5,
          left: 10,
          right: 10,
          bottom: 5
        }
      },
      colors: [config.colors.danger],
      fill: {
        type: 'gradient',
        gradient: {
          shade: shadeColor,
          type: 'horizontal',
          gradientToColors: undefined,
          opacityFrom: 0,
          opacityTo: 0.9,
          stops: [0, 30, 70, 100]
        }
      },
      xaxis: {
        labels: {
          show: false
        },
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof expensesLineChartEl !== undefined && expensesLineChartEl !== null) {
    const expensesLineChart = new ApexCharts(expensesLineChartEl, expensesLineChartConfig);
    expensesLineChart.render();
  }

  // Report Chart
  // --------------------------------------------------------------------

  // Radial bar chart functions
  function radialBarChart(color, value) {
    const radialBarChartOpt = {
      chart: {
        height: 55,
        width: 40,
        type: 'radialBar'
      },
      plotOptions: {
        radialBar: {
          hollow: {
            size: '32%'
          },
          dataLabels: {
            show: false
          },
          track: {
            background: borderColor
          }
        }
      },
      colors: [color],
      grid: {
        padding: {
          top: -10,
          bottom: -10,
          left: -5,
          right: 0
        }
      },
      series: [value],
      labels: ['Progress']
    };
    return radialBarChartOpt;
  }

  const ReportchartList = document.querySelectorAll('.chart-report');
  if (ReportchartList) {
    ReportchartList.forEach(function (ReportchartEl) {
      const color = config.colors[ReportchartEl.dataset.color],
        series = ReportchartEl.dataset.series;
      const optionsBundle = radialBarChart(color, series);
      const reportChart = new ApexCharts(ReportchartEl, optionsBundle);
      reportChart.render();
    });
  }

  // Registrations Bar Chart
  // --------------------------------------------------------------------
  const registrationsBarChartEl = document.querySelector('#registrationsBarChart'),
    registrationsBarChartConfig = {
      chart: {
        height: 70,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          barHeight: '80%',
          columnWidth: '50%',
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadius: 2,
          distributed: true
        }
      },
      tooltip: {
        enabled: false
      },
      grid: {
        show: false,
        padding: {
          top: -20,
          bottom: -12,
          left: 0,
          right: 0
        }
      },
      colors: [
        config.colors_label.warning,
        config.colors_label.warning,
        config.colors_label.warning,
        config.colors_label.warning,
        config.colors.warning,
        config.colors_label.warning,
        config.colors_label.warning
      ],
      dataLabels: {
        enabled: false
      },
      series: [
        {
          data: [30, 55, 45, 95, 70, 50, 65]
        }
      ],
      legend: {
        show: false
      },
      xaxis: {
        categories: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof registrationsBarChartEl !== undefined && registrationsBarChartEl !== null) {
    const registrationsBarChart = new ApexCharts(registrationsBarChartEl, registrationsBarChartConfig);
    registrationsBarChart.render();
  }

  // Visits Bar Chart
  // --------------------------------------------------------------------
  const visitsBarChartEl = document.querySelector('#visitsBarChart'),
    visitsBarChartConfig = {
      chart: {
        height: 70,
        type: 'bar',
        toolbar: {
          show: false
        }
      },
      plotOptions: {
        bar: {
          barHeight: '80%',
          columnWidth: '50%',
          startingShape: 'rounded',
          endingShape: 'rounded',
          borderRadius: 2,
          distributed: true
        }
      },
      tooltip: {
        enabled: false
      },
      grid: {
        show: false,
        padding: {
          top: -20,
          bottom: -12,
          left: 0,
          right: 0
        }
      },
      colors: [
        config.colors_label.success,
        config.colors_label.success,
        config.colors_label.success,
        config.colors_label.success,
        config.colors.success,
        config.colors_label.success,
        config.colors_label.success
      ],
      dataLabels: {
        enabled: false
      },
      series: [
        {
          data: [15, 42, 33, 54, 98, 48, 37]
        }
      ],
      legend: {
        show: false
      },
      xaxis: {
        categories: ['M', 'T', 'W', 'T', 'F', 'S', 'S'],
        axisBorder: {
          show: false
        },
        axisTicks: {
          show: false
        },
        labels: {
          show: false
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof visitsBarChartEl !== undefined && visitsBarChartEl !== null) {
    const visitsBarChart = new ApexCharts(visitsBarChartEl, visitsBarChartConfig);
    visitsBarChart.render();
  }

  // Registrations - Line Chart
  // --------------------------------------------------------------------
  const registrationChartEl = document.querySelector('#registrationsChart'),
    registrationChartConfig = {
      series: [
        {
          data: [57, 25, 94, 32, 98, 81, 125]
        }
      ],
      chart: {
        height: 120,
        parentHeightOffset: 0,
        parentWidthOffset: 0,
        type: 'line',
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 3,
        curve: 'straight'
      },
      grid: {
        show: false,
        padding: {
          top: -30,
          left: 2,
          right: 0,
          bottom: -10
        }
      },
      colors: [config.colors.success],
      xaxis: {
        show: false,
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        axisBorder: {
          show: true,
          color: borderColor
        },
        axisTicks: {
          show: true,
          color: borderColor
        },
        labels: {
          show: true,
          style: {
            fontSize: '0.813rem',
            fontFamily: 'IBM Plex Sans',
            colors: labelColor
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof registrationChartEl !== undefined && registrationChartEl !== null) {
    const registrationChart = new ApexCharts(registrationChartEl, registrationChartConfig);
    registrationChart.render();
  }

  // Expenses - Line Chart
  // --------------------------------------------------------------------
  const expensesChartEl = document.querySelector('#expensesChart'),
    expensesChartConfig = {
      series: [
        {
          data: [115, 70, 105, 34, 122, 21, 62]
        }
      ],
      chart: {
        height: 120,
        parentHeightOffset: 0,
        parentWidthOffset: 0,
        type: 'line',
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 3,
        curve: 'straight'
      },
      grid: {
        show: false,
        padding: {
          top: -30,
          left: 2,
          right: 0,
          bottom: -10
        }
      },
      colors: [config.colors.danger],
      xaxis: {
        show: false,
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        axisBorder: {
          show: true,
          color: borderColor
        },
        axisTicks: {
          show: true,
          color: borderColor
        },
        labels: {
          show: true,
          style: {
            fontSize: '0.813rem',
            fontFamily: 'IBM Plex Sans',
            colors: labelColor
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof expensesChartEl !== undefined && expensesChartEl !== null) {
    const expensesChart = new ApexCharts(expensesChartEl, expensesChartConfig);
    expensesChart.render();
  }

  // Users - Line Chart
  // --------------------------------------------------------------------
  const usersChartEl = document.querySelector('#usersChart'),
    usersChartConfig = {
      series: [
        {
          data: [58, 27, 141, 60, 98, 31, 165]
        }
      ],
      chart: {
        height: 120,
        parentHeightOffset: 0,
        parentWidthOffset: 0,
        type: 'line',
        toolbar: {
          show: false
        }
      },
      dataLabels: {
        enabled: false
      },
      stroke: {
        width: 3,
        curve: 'straight'
      },
      grid: {
        show: false,
        padding: {
          top: -30,
          left: 2,
          right: 0,
          bottom: -10
        }
      },
      colors: [config.colors.primary],
      xaxis: {
        show: false,
        categories: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
        axisBorder: {
          show: true,
          color: borderColor
        },
        axisTicks: {
          show: true,
          color: borderColor
        },
        labels: {
          show: true,
          style: {
            fontSize: '0.813rem',
            fontFamily: 'IBM Plex Sans',
            colors: labelColor
          }
        }
      },
      yaxis: {
        labels: {
          show: false
        }
      }
    };
  if (typeof usersChartEl !== undefined && usersChartEl !== null) {
    const usersChart = new ApexCharts(usersChartEl, usersChartConfig);
    usersChart.render();
  }
})();
