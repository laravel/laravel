'use strict';
$(function () {
  barChart();
  barChartWithImg();
  lineChart();
  donutChart();
  pieChart();
  gaugeChart();
  radialLineChart();
  dumbbellPlotChart();
  mapBubble();
});

function barChart() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end



  // Create chart instance
  var chart = am4core.create("barChart", am4charts.XYChart);
  chart.scrollbarX = new am4core.Scrollbar();

  // Add data
  chart.data = [{
    "country": "USA",
    "visits": 3025
  }, {
    "country": "China",
    "visits": 1882
  }, {
    "country": "Japan",
    "visits": 1809
  }, {
    "country": "Germany",
    "visits": 1322
  }, {
    "country": "UK",
    "visits": 1122
  }, {
    "country": "France",
    "visits": 1114
  }, {
    "country": "India",
    "visits": 984
  }, {
    "country": "Spain",
    "visits": 711
  }, {
    "country": "Netherlands",
    "visits": 665
  }, {
    "country": "Russia",
    "visits": 580
  }, {
    "country": "South Korea",
    "visits": 443
  }, {
    "country": "Canada",
    "visits": 441
  }];

  // Create axes
  var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
  categoryAxis.dataFields.category = "country";
  categoryAxis.renderer.grid.template.location = 0;
  categoryAxis.renderer.minGridDistance = 30;
  categoryAxis.renderer.labels.template.horizontalCenter = "right";
  categoryAxis.renderer.labels.template.verticalCenter = "middle";
  categoryAxis.renderer.labels.template.rotation = 270;
  categoryAxis.tooltip.disabled = true;
  categoryAxis.renderer.minHeight = 110;
  categoryAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
  valueAxis.renderer.minWidth = 50;
  valueAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  // Create series
  var series = chart.series.push(new am4charts.ColumnSeries());
  series.sequencedInterpolation = true;
  series.dataFields.valueY = "visits";
  series.dataFields.categoryX = "country";
  series.tooltipText = "[{categoryX}: bold]{valueY}[/]";
  series.columns.template.strokeWidth = 0;


  series.tooltip.pointerOrientation = "vertical";

  series.columns.template.column.cornerRadiusTopLeft = 10;
  series.columns.template.column.cornerRadiusTopRight = 10;
  series.columns.template.column.fillOpacity = 0.8;

  // on hover, make corner radiuses bigger
  let hoverState = series.columns.template.column.states.create("hover");
  hoverState.properties.cornerRadiusTopLeft = 0;
  hoverState.properties.cornerRadiusTopRight = 0;
  hoverState.properties.fillOpacity = 1;

  series.columns.template.adapter.add("fill", (fill, target) => {
    return chart.colors.getIndex(target.dataItem.index);
  })

  // Cursor
  chart.cursor = new am4charts.XYCursor();
}
function barChartWithImg() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  // Create chart instance
  var chart = am4core.create("barImg", am4charts.XYChart);

  // Add data
  chart.data = [{
    "name": "John",
    "points": 35654,
    "color": chart.colors.next(),
    "bullet": "assets/img/users/user1-round.png"
  }, {
    "name": "Damon",
    "points": 65456,
    "color": chart.colors.next(),
    "bullet": "assets/img/users/user2-round.png"
  }, {
    "name": "Patrick",
    "points": 45724,
    "color": chart.colors.next(),
    "bullet": "assets/img/users/user3-round.png"
  }, {
    "name": "Sarah",
    "points": 13654,
    "color": chart.colors.next(),
    "bullet": "assets/img/users/user4-round.png"
  },
  {
    "name": "Pooja",
    "points": 32589,
    "color": chart.colors.next(),
    "bullet": "assets/img/users/user5-round.png"
  },
  {
    "name": "jatin",
    "points": 45895,
    "color": chart.colors.next(),
    "bullet": "assets/img/users/user6-round.png"
  },
  ];

  // Create axes
  var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
  categoryAxis.dataFields.category = "name";
  categoryAxis.renderer.grid.template.disabled = true;
  categoryAxis.renderer.minGridDistance = 30;
  categoryAxis.renderer.inside = true;
  categoryAxis.renderer.labels.template.fill = am4core.color("#fff");
  categoryAxis.renderer.labels.template.fontSize = 14;

  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
  valueAxis.renderer.grid.template.strokeDasharray = "4,4";
  valueAxis.renderer.labels.template.disabled = true;
  valueAxis.min = 0;

  // Do not crop bullets
  chart.maskBullets = false;

  // Remove padding
  chart.paddingBottom = 0;

  // Create series
  var series = chart.series.push(new am4charts.ColumnSeries());
  series.dataFields.valueY = "points";
  series.dataFields.categoryX = "name";
  series.columns.template.propertyFields.fill = "color";
  series.columns.template.propertyFields.stroke = "color";
  series.columns.template.column.cornerRadiusTopLeft = 15;
  series.columns.template.column.cornerRadiusTopRight = 15;
  series.columns.template.tooltipText = "{categoryX}: [bold]{valueY}[/b]";

  // Add bullets
  var bullet = series.bullets.push(new am4charts.Bullet());
  var image = bullet.createChild(am4core.Image);
  image.horizontalCenter = "middle";
  image.verticalCenter = "bottom";
  image.dy = 20;
  image.y = am4core.percent(100);
  image.propertyFields.href = "bullet";
  image.tooltipText = series.columns.template.tooltipText;
  image.propertyFields.fill = "color";
  image.filters.push(new am4core.DropShadowFilter());
}

function lineChart() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  // Create chart instance
  var chart = am4core.create("lineChart", am4charts.XYChart);

  // Add data
  chart.data = [{
    "date": "2012-08-12",
    "value": 32
  }, {
    "date": "2012-08-13",
    "value": 18
  }, {
    "date": "2012-08-14",
    "value": 24
  }, {
    "date": "2012-08-15",
    "value": 22
  }, {
    "date": "2012-08-16",
    "value": 18
  }, {
    "date": "2012-08-17",
    "value": 19
  }, {
    "date": "2012-08-18",
    "value": 14
  }, {
    "date": "2012-08-19",
    "value": 15
  }, {
    "date": "2012-08-20",
    "value": 12
  }, {
    "date": "2012-08-28",
    "value": 18
  }, {
    "date": "2012-08-29",
    "value": 20
  }, {
    "date": "2012-08-30",
    "value": 29
  }, {
    "date": "2012-08-31",
    "value": 33
  }, {
    "date": "2012-09-01",
    "value": 42
  }, {
    "date": "2012-09-02",
    "value": 35
  }, {
    "date": "2012-09-03",
    "value": 31
  }, {
    "date": "2012-09-04",
    "value": 47
  }, {
    "date": "2012-09-05",
    "value": 52
  }, {
    "date": "2012-09-06",
    "value": 46
  }, {
    "date": "2012-09-07",
    "value": 41
  }, {
    "date": "2012-09-08",
    "value": 43
  }, {
    "date": "2012-09-09",
    "value": 40
  }, {
    "date": "2012-09-10",
    "value": 39
  }, {
    "date": "2012-09-11",
    "value": 34
  }, {
    "date": "2012-09-12",
    "value": 29
  }, {
    "date": "2012-09-13",
    "value": 34
  }, {
    "date": "2012-11-14",
    "value": 81
  }, {
    "date": "2012-11-15",
    "value": 87
  }, {
    "date": "2012-11-16",
    "value": 82
  }, {
    "date": "2012-11-17",
    "value": 86
  }, {
    "date": "2012-11-18",
    "value": 80
  }, {
    "date": "2012-11-19",
    "value": 87
  }, {
    "date": "2012-11-20",
    "value": 83
  }, {
    "date": "2012-11-21",
    "value": 85
  }, {
    "date": "2012-11-22",
    "value": 84
  }, {
    "date": "2012-11-23",
    "value": 82
  }, {
    "date": "2012-11-24",
    "value": 73
  }, {
    "date": "2012-11-25",
    "value": 71
  }, {
    "date": "2012-11-26",
    "value": 75
  }, {
    "date": "2012-11-27",
    "value": 79
  }, {
    "date": "2012-11-28",
    "value": 70
  }, {
    "date": "2012-11-29",
    "value": 73
  }, {
    "date": "2012-11-30",
    "value": 61
  }, {
    "date": "2012-12-01",
    "value": 62
  }, {
    "date": "2012-12-02",
    "value": 66
  }, {
    "date": "2012-12-03",
    "value": 65
  }, {
    "date": "2012-12-04",
    "value": 73
  }, {
    "date": "2012-12-05",
    "value": 79
  }, {
    "date": "2012-12-06",
    "value": 78
  }, {
    "date": "2012-12-07",
    "value": 78
  }, {
    "date": "2012-12-08",
    "value": 78
  }, {
    "date": "2012-12-09",
    "value": 74
  }, {
    "date": "2012-12-10",
    "value": 73
  }, {
    "date": "2012-12-11",
    "value": 75
  }, {
    "date": "2012-12-12",
    "value": 70
  }, {
    "date": "2012-12-13",
    "value": 77
  }, {
    "date": "2012-12-14",
    "value": 67
  }, {
    "date": "2012-12-15",
    "value": 62
  }, {
    "date": "2012-12-16",
    "value": 64
  }, {
    "date": "2012-12-17",
    "value": 61
  }, {
    "date": "2012-12-18",
    "value": 59
  }, {
    "date": "2012-12-19",
    "value": 53
  }, {
    "date": "2012-12-20",
    "value": 54
  }, {
    "date": "2012-12-21",
    "value": 56
  }, {
    "date": "2012-12-22",
    "value": 59
  }, {
    "date": "2012-12-23",
    "value": 58
  }, {
    "date": "2012-12-24",
    "value": 55
  }, {
    "date": "2012-12-25",
    "value": 52
  }, {
    "date": "2012-12-26",
    "value": 54
  }, {
    "date": "2012-12-27",
    "value": 50
  }, {
    "date": "2012-12-28",
    "value": 50
  }, {
    "date": "2012-12-29",
    "value": 51
  }, {
    "date": "2012-12-30",
    "value": 52
  }, {
    "date": "2012-12-31",
    "value": 58
  }, {
    "date": "2013-01-01",
    "value": 60
  }, {
    "date": "2013-01-02",
    "value": 67
  }, {
    "date": "2013-01-03",
    "value": 64
  }, {
    "date": "2013-01-04",
    "value": 66
  }, {
    "date": "2013-01-05",
    "value": 60
  }, {
    "date": "2013-01-06",
    "value": 63
  }, {
    "date": "2013-01-07",
    "value": 61
  }, {
    "date": "2013-01-08",
    "value": 60
  }, {
    "date": "2013-01-09",
    "value": 65
  }, {
    "date": "2013-01-10",
    "value": 75
  }, {
    "date": "2013-01-11",
    "value": 77
  }, {
    "date": "2013-01-12",
    "value": 78
  }, {
    "date": "2013-01-13",
    "value": 70
  }, {
    "date": "2013-01-14",
    "value": 70
  }, {
    "date": "2013-01-15",
    "value": 73
  }, {
    "date": "2013-01-16",
    "value": 71
  }, {
    "date": "2013-01-17",
    "value": 74
  }, {
    "date": "2013-01-18",
    "value": 78
  }, {
    "date": "2013-01-19",
    "value": 85
  }, {
    "date": "2013-01-20",
    "value": 82
  }, {
    "date": "2013-01-21",
    "value": 83
  }, {
    "date": "2013-01-22",
    "value": 88
  }, {
    "date": "2013-01-23",
    "value": 85
  }, {
    "date": "2013-01-24",
    "value": 85
  }, {
    "date": "2013-01-25",
    "value": 80
  }, {
    "date": "2013-01-26",
    "value": 87
  }, {
    "date": "2013-01-27",
    "value": 84
  }, {
    "date": "2013-01-28",
    "value": 83
  }, {
    "date": "2013-01-29",
    "value": 84
  }, {
    "date": "2013-01-30",
    "value": 81
  }];

  // Create axes
  var dateAxis = chart.xAxes.push(new am4charts.DateAxis());
  dateAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");
  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
  valueAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  // Create series
  var series = chart.series.push(new am4charts.LineSeries());
  series.dataFields.valueY = "value";
  series.dataFields.dateX = "date";
  series.tooltipText = "{value}"
  series.strokeWidth = 2;
  series.minBulletDistance = 15;

  // Drop-shaped tooltips
  series.tooltip.background.cornerRadius = 20;
  series.tooltip.background.strokeOpacity = 0;
  series.tooltip.pointerOrientation = "vertical";
  series.tooltip.label.minWidth = 40;
  series.tooltip.label.minHeight = 40;
  series.tooltip.label.textAlign = "middle";
  series.tooltip.label.textValign = "middle";

  // Make bullets grow on hover
  var bullet = series.bullets.push(new am4charts.CircleBullet());
  bullet.circle.strokeWidth = 2;
  bullet.circle.radius = 4;
  bullet.circle.fill = am4core.color("#fff");

  var bullethover = bullet.states.create("hover");
  bullethover.properties.scale = 1.3;

  // Make a panning cursor
  chart.cursor = new am4charts.XYCursor();
  chart.cursor.behavior = "panXY";
  chart.cursor.xAxis = dateAxis;
  chart.cursor.snapToSeries = series;

  // Create vertical scrollbar and place it before the value axis
  chart.scrollbarY = new am4core.Scrollbar();
  chart.scrollbarY.parent = chart.leftAxesContainer;
  chart.scrollbarY.toBack();

  // Create a horizontal scrollbar with previe and place it underneath the date axis
  chart.scrollbarX = new am4charts.XYChartScrollbar();
  chart.scrollbarX.series.push(series);
  chart.scrollbarX.parent = chart.bottomAxesContainer;

  chart.events.on("ready", function () {
    dateAxis.zoom({ start: 0.90, end: 1 });
  });

}

function donutChart() {

  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  // Create chart instance
  var chart = am4core.create("donutChart", am4charts.PieChart);

  // Add data
  chart.data = [{
    "country": "Lithuania",
    "litres": 501.9
  }, {
    "country": "Czech Republic",
    "litres": 301.9
  }, {
    "country": "Ireland",
    "litres": 201.1
  }, {
    "country": "Germany",
    "litres": 165.8
  }, {
    "country": "Australia",
    "litres": 139.9
  }, {
    "country": "Austria",
    "litres": 128.3
  }, {
    "country": "UK",
    "litres": 99
  }, {
    "country": "Belgium",
    "litres": 60
  }, {
    "country": "The Netherlands",
    "litres": 50
  }];

  // Set inner radius
  chart.innerRadius = am4core.percent(50);

  // Add and configure Series
  var pieSeries = chart.series.push(new am4charts.PieSeries());
  pieSeries.dataFields.value = "litres";
  pieSeries.dataFields.category = "country";
  pieSeries.slices.template.stroke = am4core.color("#fff");
  pieSeries.slices.template.strokeWidth = 2;
  pieSeries.slices.template.strokeOpacity = 1;
  pieSeries.labels.template.fill = am4core.color("#9aa0ac");

  // This creates initial animation
  pieSeries.hiddenState.properties.opacity = 1;
  pieSeries.hiddenState.properties.endAngle = -90;
  pieSeries.hiddenState.properties.startAngle = -90;
}

function pieChart() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  // Create chart instance
  var chart = am4core.create("pieChart", am4charts.PieChart);

  // Add data
  chart.data = [{
    "country": "Lithuania",
    "litres": 501.9
  }, {
    "country": "Czech Republic",
    "litres": 301.9
  }, {
    "country": "Ireland",
    "litres": 201.1
  }, {
    "country": "Germany",
    "litres": 165.8
  }, {
    "country": "Australia",
    "litres": 139.9
  }, {
    "country": "Austria",
    "litres": 128.3
  }, {
    "country": "UK",
    "litres": 99
  }, {
    "country": "Belgium",
    "litres": 60
  }, {
    "country": "The Netherlands",
    "litres": 50
  }];

  // Add and configure Series
  var pieSeries = chart.series.push(new am4charts.PieSeries());
  pieSeries.dataFields.value = "litres";
  pieSeries.dataFields.category = "country";
  pieSeries.slices.template.stroke = am4core.color("#fff");
  pieSeries.slices.template.strokeWidth = 2;
  pieSeries.slices.template.strokeOpacity = 1;
  pieSeries.labels.template.fill = am4core.color("#9aa0ac");

  // This creates initial animation
  pieSeries.hiddenState.properties.opacity = 1;
  pieSeries.hiddenState.properties.endAngle = -90;
  pieSeries.hiddenState.properties.startAngle = -90;
}

function gaugeChart() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end



  // Create chart instance
  var chart = am4core.create("gaugeChart", am4charts.RadarChart);

  // Add data
  chart.data = [{
    "category": "Research",
    "value": 80,
    "full": 100
  }, {
    "category": "Marketing",
    "value": 35,
    "full": 100
  }, {
    "category": "Distribution",
    "value": 92,
    "full": 100
  }, {
    "category": "Human Resources",
    "value": 68,
    "full": 100
  }];

  // Make chart not full circle
  chart.startAngle = -90;
  chart.endAngle = 180;
  chart.innerRadius = am4core.percent(20);

  // Set number format
  chart.numberFormatter.numberFormat = "#.#'%'";

  // Create axes
  var categoryAxis = chart.yAxes.push(new am4charts.CategoryAxis());
  categoryAxis.dataFields.category = "category";
  categoryAxis.renderer.grid.template.location = 0;
  categoryAxis.renderer.grid.template.strokeOpacity = 0;
  categoryAxis.renderer.labels.template.horizontalCenter = "right";
  categoryAxis.renderer.labels.template.fontWeight = 500;
  categoryAxis.renderer.labels.template.adapter.add("fill", function (fill, target) {
    return (target.dataItem.index >= 0) ? chart.colors.getIndex(target.dataItem.index) : fill;
  });
  categoryAxis.renderer.minGridDistance = 10;

  var valueAxis = chart.xAxes.push(new am4charts.ValueAxis());
  valueAxis.renderer.grid.template.strokeOpacity = 0;
  valueAxis.min = 0;
  valueAxis.max = 100;
  valueAxis.strictMinMax = true;
  valueAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  // Create series
  var series1 = chart.series.push(new am4charts.RadarColumnSeries());
  series1.dataFields.valueX = "full";
  series1.dataFields.categoryY = "category";
  series1.clustered = false;
  series1.columns.template.fill = new am4core.InterfaceColorSet().getFor("alternativeBackground");
  series1.columns.template.fillOpacity = 0.08;
  series1.columns.template.cornerRadiusTopLeft = 20;
  series1.columns.template.strokeWidth = 0;
  series1.columns.template.radarColumn.cornerRadius = 20;

  var series2 = chart.series.push(new am4charts.RadarColumnSeries());
  series2.dataFields.valueX = "value";
  series2.dataFields.categoryY = "category";
  series2.clustered = false;
  series2.columns.template.strokeWidth = 0;
  series2.columns.template.tooltipText = "{category}: [bold]{value}[/]";
  series2.columns.template.radarColumn.cornerRadius = 20;

  series2.columns.template.adapter.add("fill", function (fill, target) {
    return chart.colors.getIndex(target.dataItem.index);
  });

  // Add cursor
  chart.cursor = new am4charts.RadarCursor();
}
function radialLineChart() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  /* Create chart instance */
  var chart = am4core.create("radialLineChart", am4charts.RadarChart);

  var data = [];
  var value1 = 500;
  var value2 = 600;

  for (var i = 0; i < 12; i++) {
    let date = new Date();
    date.setMonth(i, 1);
    value1 -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 50);
    value2 -= Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 50);
    data.push({ date: date, value1: value1, value2: value2 })
  }

  chart.data = data;

  /* Create axes */
  var categoryAxis = chart.xAxes.push(new am4charts.DateAxis());
  categoryAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
  valueAxis.extraMin = 0.2;
  valueAxis.extraMax = 0.2;
  valueAxis.tooltip.disabled = true;
  valueAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  /* Create and configure series */
  var series1 = chart.series.push(new am4charts.RadarSeries());
  series1.dataFields.valueY = "value1";
  series1.dataFields.dateX = "date";
  series1.strokeWidth = 3;
  series1.tooltipText = "{valueY}";
  series1.name = "Series 2";
  series1.bullets.create(am4charts.CircleBullet);

  var series2 = chart.series.push(new am4charts.RadarSeries());
  series2.dataFields.valueY = "value2";
  series2.dataFields.dateX = "date";
  series2.strokeWidth = 3;
  series2.tooltipText = "{valueY}";
  series2.name = "Series 2";
  series2.bullets.create(am4charts.CircleBullet);

  chart.scrollbarX = new am4core.Scrollbar();
  chart.scrollbarY = new am4core.Scrollbar();

  chart.cursor = new am4charts.RadarCursor();

  chart.legend = new am4charts.Legend();
}
function dumbbellPlotChart() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  var chart = am4core.create("dumbbellPlotChart", am4charts.XYChart);

  var data = [];
  var open = 100;
  var close = 120;

  var names = ["Raina",
    "Demarcus",
    "Carlo",
    "Jacinda",
    "Richie",
    "Antony",
    "Amada",
    "Idalia",
    "Janella",
    "Marla",
    "Curtis",
    "Shellie"

  ];

  for (var i = 0; i < names.length; i++) {
    open += Math.round((Math.random() < 0.5 ? 1 : -1) * Math.random() * 5);
    close = open + Math.round(Math.random() * 10) + 3;
    data.push({ category: names[i], open: open, close: close });
  }

  chart.data = data;

  var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
  categoryAxis.renderer.grid.template.location = 0;
  categoryAxis.renderer.ticks.template.disabled = true;
  categoryAxis.renderer.axisFills.template.disabled = true;
  categoryAxis.dataFields.category = "category";
  categoryAxis.renderer.minGridDistance = 15;
  categoryAxis.renderer.grid.template.location = 0.5;
  categoryAxis.renderer.grid.template.strokeDasharray = "1,3";
  categoryAxis.renderer.labels.template.rotation = -90;
  categoryAxis.renderer.labels.template.horizontalCenter = "left";
  categoryAxis.renderer.labels.template.dx = 17;
  categoryAxis.renderer.inside = true;
  categoryAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
  valueAxis.tooltip.disabled = true;
  valueAxis.renderer.ticks.template.disabled = true;
  valueAxis.renderer.axisFills.template.disabled = true;
  valueAxis.renderer.labels.template.fill = am4core.color("#9aa0ac");

  var series = chart.series.push(new am4charts.ColumnSeries());
  series.dataFields.categoryX = "category";
  series.dataFields.openValueY = "open";
  series.dataFields.valueY = "close";
  series.tooltipText = "open: {openValueY.value} close: {valueY.value}";
  series.sequencedInterpolation = true;
  series.fillOpacity = 0;
  series.strokeOpacity = 1;
  series.columns.template.width = 0.01;
  series.tooltip.pointerOrientation = "horizontal";

  var openBullet = series.bullets.create(am4charts.CircleBullet);
  openBullet.locationY = 1;

  var closeBullet = series.bullets.create(am4charts.CircleBullet);

  closeBullet.fill = chart.colors.getIndex(4);
  closeBullet.stroke = closeBullet.fill;

  chart.cursor = new am4charts.XYCursor();

  chart.scrollbarX = new am4core.Scrollbar();
  chart.scrollbarY = new am4core.Scrollbar();
}

function mapBubble() {
  // Themes begin
  am4core.useTheme(am4themes_animated);
  // Themes end

  // Create map instance
  var chart = am4core.create("mapBubble", am4maps.MapChart);

  var title = chart.titles.create();
  title.text = "[bold font-size: 20]Population of Countries in 2011[/]\nsource: Gapminder";
  title.textAlign = "middle";
  

  var latlong = {
    "IN": { "latitude": 20, "longitude": 77 },
    "JP": { "latitude": 36, "longitude": 138 },
    "AU": { "latitude": -27, "longitude": 133 },
    "US": { "latitude": 38, "longitude": -97 },
    "RU": { "latitude": 60, "longitude": 100 },
    "BR": { "latitude": -10, "longitude": -55 },
    "DZ": { "latitude": 28, "longitude": 3 }
  };

  var mapData = [
    { "id": "IN", "name": "India", "value": 1241491960, "color": chart.colors.getIndex(0) },
    { "id": "JP", "name": "Japan", "value": 126497241, "color": chart.colors.getIndex(0) },
    { "id": "AU", "name": "Australia", "value": 22605732, "color": "#8aabb0" },
    { "id": "US", "name": "United States", "value": 313085380, "color": chart.colors.getIndex(4) },
    { "id": "RU", "name": "Russia", "value": 142835555, "color": chart.colors.getIndex(1) },
    { "id": "BR", "name": "Brazil", "value": 196655014, "color": chart.colors.getIndex(3) },
    { "id": "DZ", "name": "Algeria", "value": 35980193, "color": chart.colors.getIndex(2) }
  ];

  // Add lat/long information to data
  for (var i = 0; i < mapData.length; i++) {
    mapData[i].latitude = latlong[mapData[i].id].latitude;
    mapData[i].longitude = latlong[mapData[i].id].longitude;
  }

  // Set map definition
  chart.geodata = am4geodata_worldLow;

  // Set projection
  chart.projection = new am4maps.projections.Miller();

  // Create map polygon series
  var polygonSeries = chart.series.push(new am4maps.MapPolygonSeries());
  polygonSeries.exclude = ["AQ"];
  polygonSeries.useGeodata = true;

  var imageSeries = chart.series.push(new am4maps.MapImageSeries());
  imageSeries.data = mapData;
  imageSeries.dataFields.value = "value";

  var imageTemplate = imageSeries.mapImages.template;
  imageTemplate.propertyFields.latitude = "latitude";
  imageTemplate.propertyFields.longitude = "longitude";
  imageTemplate.nonScaling = true

  var circle = imageTemplate.createChild(am4core.Circle);
  circle.fillOpacity = 0.7;
  circle.propertyFields.fill = "color";
  circle.tooltipText = "{name}: [bold]{value}[/]";

  imageSeries.heatRules.push({
    "target": circle,
    "property": "radius",
    "min": 4,
    "max": 30,
    "dataField": "value"
  })
}