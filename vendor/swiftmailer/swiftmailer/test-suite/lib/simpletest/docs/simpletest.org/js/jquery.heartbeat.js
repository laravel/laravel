$(document).ready(function() {
    $('#tests-results').load('../views/heartbeat.php?tests-results');
    $('#commits').load('../views/heartbeat.php', '', function() {
        $(this).find(".sparkline").each(function() {
            sparklinequery($(this));
        });
    });
    $('#last-commits').load('../views/heartbeat.php?last-commits');
});
