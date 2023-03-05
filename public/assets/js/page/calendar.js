
var today = new Date();
year = today.getFullYear();
month = today.getMonth();
day = today.getDate();
var calendar = $('#myEvent').fullCalendar({
  height: 'auto',
  defaultView: 'month',
  editable: true,
  selectable: true,
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay,listMonth'
  },
  events: [{
    title: "Palak Jani",
    start: new Date(year, month, day, 11, 30),
    end: new Date(year, month, day, 12, 00),
    backgroundColor: "#00bcd4"
  }, {
    title: "Priya Sarma",
    start: new Date(year, month, day, 13, 30),
    end: new Date(year, month, day, 14, 00),
    backgroundColor: "#fe9701"
  }, {
    title: "John Doe",
    start: new Date(year, month, day, 17, 30),
    end: new Date(year, month, day, 18, 00),
    backgroundColor: "#F3565D"
  }, {
    title: "Sarah Smith",
    start: new Date(year, month, day, 19, 00),
    end: new Date(year, month, day, 19, 30),
    backgroundColor: "#1bbc9b"
  }, {
    title: "Airi Satou",
    start: new Date(year, month, day + 1, 19, 00),
    end: new Date(year, month, day + 1, 19, 30),
    backgroundColor: "#DC35A9",
  }, {
    title: "Angelica Ramos",
    start: new Date(year, month, day + 1, 21, 00),
    end: new Date(year, month, day + 1, 21, 30),
    backgroundColor: "#fe9701",
  }, {
    title: "Palak Jani",
    start: new Date(year, month, day + 3, 11, 30),
    end: new Date(year, month, day + 3, 12, 00),
    backgroundColor: "#00bcd4"
  }, {
    title: "Priya Sarma",
    start: new Date(year, month, day + 5, 2, 30),
    end: new Date(year, month, day + 5, 3, 00),
    backgroundColor: "#9b59b6"
  }, {
    title: "John Doe",
    start: new Date(year, month, day + 7, 17, 30),
    end: new Date(year, month, day + 7, 18, 00),
    backgroundColor: "#F3565D"
  }, {
    title: "Mark Hay",
    start: new Date(year, month, day + 5, 9, 30),
    end: new Date(year, month, day + 5, 10, 00),
    backgroundColor: "#F3565D"
  }]
});

/*$("#myEvent").fullCalendar({
  height: 'auto',
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay,listWeek'
  },
  editable: true,
  events: [
    {
      title: 'Conference',
      start: '2018-01-9',
      end: '2018-01-11',
      backgroundColor: "#fff",
      borderColor: "#fff",
      textColor: '#000'
    },
    {
      title: "John's Birthday",
      start: '2018-01-14',
      backgroundColor: "#007bff",
      borderColor: "#007bff",
      textColor: '#fff'
    },
    {
      title: 'Reporting',
      start: '2018-01-10T11:30:00',
      backgroundColor: "#f56954",
      borderColor: "#f56954",
      textColor: '#fff'
    },
    {
      title: 'Starting New Project',
      start: '2018-01-11',
      backgroundColor: "#ffc107",
      borderColor: "#ffc107",
      textColor: '#fff'
    },
    {
      title: 'Social Distortion Concert',
      start: '2018-01-24',
      end: '2018-01-27',
      backgroundColor: "#000",
      borderColor: "#000",
      textColor: '#fff'
    },
    {
      title: 'Lunch',
      start: '2018-01-24T13:15:00',
      backgroundColor: "#fff",
      borderColor: "#fff",
      textColor: '#000',
    },
    {
      title: 'Company Trip',
      start: '2018-01-28',
      end: '2018-01-31',
      backgroundColor: "#fff",
      borderColor: "#fff",
      textColor: '#000',
    },
  ]

});
*/