"use strict";

$("#users-carousel").owlCarousel({
  items: 4,
  margin: 20,
  autoplay: true,
  autoplayTimeout: 5000,
  loop: true,
  responsive: {
    0: {
      items: 2
    },
    578: {
      items: 4
    },
    768: {
      items: 4
    }
  }
});

// Follow function
$('.follow-btn, .following-btn').each(function () {
  var me = $(this),
    follow_text = 'Follow',
    unfollow_text = 'Following';

  me.click(function () {
    if (me.hasClass('following-btn')) {
      me.removeClass('btn-danger');
      me.removeClass('following-btn');
      me.addClass('btn-primary');
      me.html(follow_text);

    } else {
      me.removeClass('btn-primary');
      me.addClass('btn-danger');
      me.addClass('following-btn');
      me.html(unfollow_text);

    }
    return false;
  });
});

  var draw = Chart.controllers.line.prototype.draw;
  Chart.controllers.lineShadow = Chart.controllers.line.extend({
    draw: function () {
      draw.apply(this, arguments);
      var ctx = this.chart.chart.ctx;
      var _stroke = ctx.stroke;
      ctx.stroke = function () {
        ctx.save();
        ctx.shadowColor = "#00000075";
        ctx.shadowBlur = 10;
        ctx.shadowOffsetX = 8;
        ctx.shadowOffsetY = 8;
        _stroke.apply(this, arguments);
        ctx.restore();
      };
    }
  });

// card chart 1
var ctx = document.getElementById("cardChart1").getContext("2d");
var gradientStroke2 = ctx.createLinearGradient(0, 0, 700, 0);
gradientStroke2.addColorStop(0, "rgba(255, 204, 128, 1)");
gradientStroke2.addColorStop(0.5, "rgba(255, 152, 0, 1)");
gradientStroke2.addColorStop(1, "rgba(239, 108, 0, 1)");

var myChart = new Chart(ctx, {
  type: "lineShadow",
  data: {
    labels: ["2010", "2011", "2012", "2013", "2014", "2015", "2016"],
    type: "line",
    datasets: [{
      label: "Income",
      data: [0, 30, 10, 120, 50, 63, 10],
      borderColor: gradientStroke2,
      pointBorderColor: gradientStroke2,
      pointBackgroundColor: gradientStroke2,
      pointHoverBackgroundColor: gradientStroke2,
      pointHoverBorderColor: gradientStroke2,
      pointBorderWidth: 5,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 0.5,
      fill: false,
      borderWidth: 4
    }]
  },
  options: {
    legend: {
      display: false
    },
    tooltips: {},
    scales: {
      yAxes: [{
        ticks: {
          display: false //this will remove only the label
        },
        gridLines: {
          display: false,
          drawBorder: false
        }
      }],
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          display: false //this will remove only the label
        }
      }]
    }
  }
});

// card chart 2
var ctx = document.getElementById("cardChart2").getContext("2d");
var gradientStroke2 = ctx.createLinearGradient(500, 0, 0, 0);
gradientStroke2.addColorStop(0, "rgba(55, 154, 80, 1)");
gradientStroke2.addColorStop(1, "rgba(131, 210, 151, 1)");

var myChart = new Chart(ctx, {
  type: "lineShadow",
  data: {
    labels: ["2010", "2011", "2012", "2013", "2014", "2015", "2016"],
    type: "line",
    datasets: [{
      label: "Income",
      data: [0, 30, 10, 120, 50, 63, 10],
      borderColor: gradientStroke2,
      pointBorderColor: gradientStroke2,
      pointBackgroundColor: gradientStroke2,
      pointHoverBackgroundColor: gradientStroke2,
      pointHoverBorderColor: gradientStroke2,
      pointBorderWidth: 5,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 0.5,
      fill: false,
      borderWidth: 4
    }]
  },
  options: {
    legend: {
      display: false
    },
    tooltips: {},
    scales: {
      yAxes: [{
        ticks: {
          display: false //this will remove only the label
        },
        gridLines: {
          display: false,
          drawBorder: false
        }
      }],
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          display: false //this will remove only the label
        }
      }]
    }
  }
});

// card chart 3
var ctx = document.getElementById("cardChart3").getContext("2d");
var gradientStroke2 = ctx.createLinearGradient(0, 0, 700, 0);
gradientStroke2.addColorStop(0, "rgba(103, 119, 239, 1)");
gradientStroke2.addColorStop(0.5, "rgba(106, 120, 220, 1)");
gradientStroke2.addColorStop(1, "rgba(92, 103, 187, 1)");

var myChart = new Chart(ctx, {
  type: "lineShadow",
  data: {
    labels: ["2010", "2011", "2012", "2013", "2014", "2015", "2016"],
    type: "line",
    datasets: [{
      label: "Income",
      data: [0, 30, 10, 120, 50, 63, 10],
      borderColor: gradientStroke2,
      pointBorderColor: gradientStroke2,
      pointBackgroundColor: gradientStroke2,
      pointHoverBackgroundColor: gradientStroke2,
      pointHoverBorderColor: gradientStroke2,
      pointBorderWidth: 5,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 0.5,
      fill: false,
      borderWidth: 4
    }]
  },
  options: {
    legend: {
      display: false
    },
    tooltips: {},
    scales: {
      yAxes: [{
        ticks: {
          display: false //this will remove only the label
        },
        gridLines: {
          display: false,
          drawBorder: false
        }
      }],
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          display: false //this will remove only the label
        }
      }]
    }
  }
});

// card chart 4
var ctx = document.getElementById("cardChart4").getContext("2d");
var gradientStroke2 = ctx.createLinearGradient(0, 0, 700, 0);
gradientStroke2.addColorStop(0, "rgba(61, 199, 190, 1)");
gradientStroke2.addColorStop(0.5, "rgba(57, 171, 163, 1)");
gradientStroke2.addColorStop(1, "rgba(40, 142, 135, 1)");

var myChart = new Chart(ctx, {
  type: "lineShadow",
  data: {
    labels: ["2010", "2011", "2012", "2013", "2014", "2015", "2016"],
    type: "line",
    datasets: [{
      label: "Income",
      data: [0, 30, 10, 120, 50, 63, 10],
      borderColor: gradientStroke2,
      pointBorderColor: gradientStroke2,
      pointBackgroundColor: gradientStroke2,
      pointHoverBackgroundColor: gradientStroke2,
      pointHoverBorderColor: gradientStroke2,
      pointBorderWidth: 5,
      pointHoverRadius: 5,
      pointHoverBorderWidth: 1,
      pointRadius: 0.5,
      fill: false,
      borderWidth: 4
    }]
  },
  options: {
    legend: {
      display: false
    },
    tooltips: {},
    scales: {
      yAxes: [{
        ticks: {
          display: false //this will remove only the label
        },
        gridLines: {
          display: false,
          drawBorder: false
        }
      }],
      xAxes: [{
        gridLines: {
          display: false,
          drawBorder: false
        },
        ticks: {
          display: false //this will remove only the label
        }
      }]
    }
  }
});
