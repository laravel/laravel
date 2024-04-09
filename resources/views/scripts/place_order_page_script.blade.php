<script>
    $(document).ready(function() {



        $("#place_order_btn").on('click', function() {


            $.ajax({
                type: "GET",
                url: "/place-order-final-process",

                success: function(response) {

                    if (response.order_status == 1) {
                        $.toast({
                            heading: "Order Confirmed",
                            text: "Your order is confirmed...",
                            bgColor: '#3B82F6',
                            loaderBg: '#60A5FA',
                            textColor: 'white',
                            position: "top-right",
                            icon: "success",
                            hideAfter: 2800, 
                            afterHidden: function() {
                                window.location.href = "/home";

                            },
                        });


                    }


                },
                error: function(error) {

                }
            });



        });



        $("#change_address").on('click', function() {

            window.location.href = "/get-data-address";


        });


    });
</script>