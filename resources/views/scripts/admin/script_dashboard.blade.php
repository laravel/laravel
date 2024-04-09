<script src="/admin_panel/assets/vendor/libs/chartjs/chartjs.js"></script>






<script>
    $(document).ready(function() {


        $.ajax({
            type: "GET",
            url: "/get-orders-count",

            dataType: "json",
            success: function(response) {

                if (response.order_status) {


                    $("#total_order_count").text(response.total);
                    $("#cancel_order_count").text(response.cancel);
                    $("#completed_order_count").text(response.completed);
                    $("#pending_order_count").text(response.pending);
                    $("#user_count").text(response.user);
                    $("#products_count").text(response.products);
                    $("#out_of_stock_count").text(response.out_of_stock);
                    $("#today_sales_total_count").text(response.today_orders_sales);
                    $("#companies_count").text(response.total_companies);




                    if (response.monthlyTotalsInJson) {

                        var m_data = (response.monthlyTotalsInJson);

                        var months = (Object.keys(m_data));
                        var month_values = (Object.values(m_data));

                    

                        if (isDarkStyle) {
                            cardColor = config.colors_dark.cardColor;
                            headingColor = config.colors_dark.headingColor;
                            labelColor = config.colors_dark.textMuted;
                            legendColor = config.colors_dark.bodyColor;
                            borderColor = config.colors_dark.borderColor;
                        } else {
                            cardColor = config.colors.cardColor;
                            headingColor = config.colors.headingColor;
                            labelColor = config.colors.textMuted;
                            legendColor = config.colors.bodyColor;
                            borderColor = config.colors.borderColor;
                        }
                        const barChart = document.getElementById('barChart');
                        if (barChart) {
                            const barChartVar = new Chart(barChart, {
                                type: 'bar',
                                data: {
                                    labels: months,
                                    datasets: [{
                                        data: month_values,
                                        backgroundColor: '#5A8DEE',
                                        borderColor: 'transparent',
                                        maxBarThickness: 25,
                                        borderRadius: {
                                            topRight: 0,
                                            topLeft: 0
                                        }
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {
                                        duration: 500
                                    },
                                    plugins: {
                                        tooltip: {
                                            rtl: isRtl,
                                            backgroundColor: cardColor,
                                            titleColor: headingColor,
                                            bodyColor: legendColor,
                                            borderWidth: 1,
                                            borderColor: borderColor
                                        },
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                color: borderColor,
                                                drawBorder: false,
                                                borderColor: borderColor
                                            },
                                            ticks: {
                                                color: labelColor
                                            }
                                        },
                                        y: {
                                            min: 0,
                                            max: 300000,
                                            grid: {
                                                color: borderColor,
                                                drawBorder: false,
                                                borderColor: borderColor
                                            },
                                            ticks: {
                                                stepSize: 50000,
                                                color: labelColor
                                            }
                                        }
                                    }
                                }
                            });
                        }



                    }










                    if (response.monthly_user) {

                        var m_data = (response.monthly_user);

                        var months = (Object.keys(m_data));
                        var month_values = (Object.values(m_data));
                        var user_range = response.user+10;





                        if (isDarkStyle) {
                            cardColor = config.colors_dark.cardColor;
                            headingColor = config.colors_dark.headingColor;
                            labelColor = config.colors_dark.textMuted;
                            legendColor = config.colors_dark.bodyColor;
                            borderColor = config.colors_dark.borderColor;
                        } else {
                            cardColor = config.colors.cardColor;
                            headingColor = config.colors.headingColor;
                            labelColor = config.colors.textMuted;
                            legendColor = config.colors.bodyColor;
                            borderColor = config.colors.borderColor;
                        }
                        const barChart = document.getElementById('user_chart');
                        if (barChart) {
                            const barChartVar = new Chart(barChart, {
                                type: 'bar',
                                data: {
                                    labels: months,
                                    datasets: [{
                                        data: month_values,
                                        backgroundColor: '#5A8DEE',
                                        borderColor: 'transparent',
                                        maxBarThickness: 25,
                                        borderRadius: {
                                            topRight: 0,
                                            topLeft: 0
                                        }
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    animation: {
                                        duration: 500
                                    },
                                    plugins: {
                                        tooltip: {
                                            rtl: isRtl,
                                            backgroundColor: cardColor,
                                            titleColor: headingColor,
                                            bodyColor: legendColor,
                                            borderWidth: 1,
                                            borderColor: borderColor
                                        },
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        x: {
                                            grid: {
                                                color: borderColor,
                                                drawBorder: false,
                                                borderColor: borderColor
                                            },
                                            ticks: {
                                                color: labelColor
                                            }
                                        },
                                        y: {
                                            min: 0,
                                            max: user_range,
                                            grid: {
                                                color: borderColor,
                                                drawBorder: false,
                                                borderColor: borderColor
                                            },
                                            ticks: {
                                                stepSize: 1,
                                                color: labelColor
                                            }
                                        }
                                    }
                                }
                            });
                        }



                    }







                }


            },
            error: function(error) {

            }
        });



    });
</script>