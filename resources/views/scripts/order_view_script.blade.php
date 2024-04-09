<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $(".cancel_order_btn").on('click', function() {

            var id = $(this).attr('id');

            $.ajax({
                type: "POST",
                url: "/cancel-order-btn-process",
                data: {
                    id: id
                },
                dataType: "json",
                success: function(response) {


                    if (response.status_cancel_order == 1) {

                        $.toast({
                            heading: "Order Cancellation",
                            text: "Your order has been cancelled successfully.",
                            bgColor: '#3B82F6',
                            loaderBg: '#60A5FA',
                            textColor: 'white',
                            position: "top-right",
                            icon: "info",
                            hideAfter: 2500,
                            afterShown: function () {
                                location.reload();
                            }, 
                        });

                    }





                },
                error: function(error) {

                }
            });


        });


    });
</script>