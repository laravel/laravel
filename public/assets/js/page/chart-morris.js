'use strict';
$(function () {
    getMorris('line', 'line_chart');
    getMorris('bar', 'bar_chart');
    getMorris('area', 'area_chart');
    getMorris('donut', 'donut_chart');
});


function getMorris(type, element) {
    if (type === 'line') {
        Morris.Line({
            element: element,
            data: [{
                period: '2008',
                iphone: 35,
                ipad: 67,
                itouch: 15
            }, {
                period: '2009',
                iphone: 140,
                ipad: 189,
                itouch: 67
            }, {
                period: '2010',
                iphone: 50,
                ipad: 80,
                itouch: 22
            }, {
                period: '2011',
                iphone: 180,
                ipad: 220,
                itouch: 76
            }, {
                period: '2012',
                iphone: 130,
                ipad: 110,
                itouch: 82
            }, {
                period: '2013',
                iphone: 80,
                ipad: 60,
                itouch: 85
            }, {
                period: '2014',
                iphone: 78,
                ipad: 205,
                itouch: 135
            }, {
                period: '2015',
                iphone: 180,
                ipad: 124,
                itouch: 140
            }, {
                period: '2016',
                iphone: 105,
                ipad: 100,
                itouch: 85
            },
            {
                period: '2017',
                iphone: 210,
                ipad: 180,
                itouch: 120
            }
            ],
            xkey: 'period',
            ykeys: ['iphone', 'ipad', 'itouch'],
            labels: ['iPhone', 'iPad', 'iPod Touch'],
            pointSize: 3,
            fillOpacity: 0,
            pointStrokeColors: ['#222222', '#cccccc', '#f96332'],
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            lineWidth: 2,
            hideHover: 'auto',
            lineColors: ['#222222', '#20B2AA', '#f96332'],
            resize: true
        });
    } else if (type === 'bar') {
        Morris.Bar({
            element: element,
            data: [{
                x: '2011 Q1',
                y: 66,
                z: 54,
                a: 38
            }, {
                x: '2011 Q2',
                y: 98,
                z: 75,
                a: 45
            }, {
                x: '2011 Q3',
                y: 73,
                z: 52,
                a: 44
            }, {
                x: '2011 Q4',
                y: 82,
                z: 64,
                a: 43
            }],
            xkey: 'x',
            ykeys: ['y', 'z', 'a'],
            labels: ['Y', 'Z', 'A'],
            barColors: ['#01B8AA', '#F2C80F', '#5F6B6D'],
            hideHover: 'auto'
        });

    } else if (type === 'area') {
        Morris.Area({
            element: element,
            data: [
                { w: '2011 Q1', x: 2, y: 0, z: 0 },
                { w: '2011 Q2', x: 50, y: 15, z: 5 },
                { w: '2011 Q3', x: 15, y: 50, z: 23 },
                { w: '2011 Q4', x: 45, y: 12, z: 7 },
                { w: '2011 Q5', x: 20, y: 32, z: 55 },
                { w: '2011 Q6', x: 39, y: 67, z: 20 },
                { w: '2011 Q7', x: 20, y: 9, z: 5 }
            ],
            xkey: 'w',
            ykeys: ['x', 'y', 'z'],
            labels: ['X', 'Y', 'Z'],
            pointSize: 0,
            lineWidth: 0,
            resize: true,
            fillOpacity: 0.8,
            behaveLikeLine: true,
            gridLineColor: '#e0e0e0',
            hideHover: 'auto',
            lineColors: ['rgb(97, 97, 97)', 'rgb(0, 206, 209)', 'rgb(255, 117, 142)']
        });
    } else if (type === 'donut') {
        Morris.Donut({
            element: element,
            data: [{
                label: 'Chrome',
                value: 37
            }, {
                label: 'Firefox',
                value: 30
            }, {
                label: 'Safari',
                value: 18
            }, {
                label: 'Opera',
                value: 12
            },
            {
                label: 'Other',
                value: 3
            }],
            colors: ['rgb(255, 206, 86)', 'rgb(65, 196, 216)', 'rgb(109, 109, 109)', 'rgb(255, 99, 132)', 'rgb(75, 192, 192)'],
            formatter: function (y) {
                return y + '%'
            }
        });
    }
}