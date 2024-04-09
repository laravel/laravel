<script>
    $(document).ready(function() {





        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        var offset = 0;
        var total_orders_count;

        function fetchData() {
            $.ajax({
                type: "POST",
                url: "/orders-page-get-data",
                data: {
                    offset: offset
                },
                success: function(response) {

                    if (response.order_list != "") {
                        $("#order_list").append(response.order_list);
                        $("#total_orders").text(response.total_orders);
                        offset += 4;
                        total_orders_count = response.total_orders;

                    }
                    if (response.total_orders == 0) {
                        $("#order_list").text("Sorry you have not any orders...").addClass(" text-lg ");
                        $("#total_orders").text(response.total_orders);
                        total_orders_count = response.total_orders;



                    }

                    // var count = $("#order_list").children().length;


                },
                error: function(error) {

                },
            });

        }



        fetchData(offset);


        $(window).scroll(function() {

            if (total_orders_count != 0) {
                if ($(window).scrollTop() + $(window).height() >= $(document).height()) {
                    fetchData(offset);

                }

            }



        });









    });
</script>