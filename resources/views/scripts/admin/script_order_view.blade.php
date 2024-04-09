<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>




<script>
    $(document).ready(function() {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $(document).on("click", ".status_modal", function() {

            var status_choice = $("#statusSelect").val(order_status);


        });



        $(document).on("change", "#statusSelect", function() {


            var status_choice = $("#statusSelect").val();

            if (status_choice == 4) {
                $("#textarea_status_reason_div").show();
            }
            if (status_choice != 4) {
                $("#textarea_status_reason_div").hide();
            }

        });


        $(document).on("click", "#payment_change_status_save_btn", function() {

            
            var order_id = "{{ $order_data->id }}";
            var payment_status = $("#payment_status_select").val();

            var data = {
                id: order_id,
                payment_status: payment_status
            };

            $.ajax({
                type: "POST",
                url: "/change-payment-status",
                dataType: "json",
                data: data,
                success: function(response) {

                    if (response.payment_status == 1) {

                        $.toast({
                            heading: "Payment Changed.",
                            text: "Order payment status changed...",
                            bgColor: '#22C55E',
                            loaderBg: '#00ff88',
                            textColor: 'white',
                            position: "top-right",
                            hideAfter: 1600,
                            icon: "success",
                            afterHidden: function() {
                                location.reload(true);

                            },
                        });
                    }


                    if (response.payment_status == 0) {

                        $.toast({
                            heading: "Payment Incomplete",
                            text: "Your payment is pending. Please complete the payment to proceed.",
                            icon: "error",
                            loaderBg: "#FFD700", 
                            bgColor: "#EF4444", 
                            textColor: "#FFFFFF", 
                            position: "top-right",
                            hideAfter: 5000, 
                            stack: false, 
                            showHideTransition: "fade", 
                            afterHidden: function() {
                                location.reload(true); 
                            }
                        });

                    }


                },
                error: function(error) {

                }
            });

        })


        $(document).on("click", "#change_status_save_btn", function() {

            var order_id = "{{ $order_data->id }}";
            var status = $("#statusSelect").val();


            var data = {
                id: order_id,
                status: status
            };

            if (status == 4) {

                var status_reason_rejected = $("#textarea_status_reason").val();

                var data = {
                    id: order_id,
                    status: status,
                    status_reason_rejected: status_reason_rejected
                };

            }

            $.ajax({
                type: "POST",
                url: "/change-order-status",
                dataType: "json",
                data: data,
                success: function(response) {

                    if (response.order_status == 1) {

                        $.toast({
                            heading: "Status Changed.",
                            text: "Order status changed...",
                            bgColor: '#22C55E',
                            loaderBg: '#00ff88',
                            textColor: 'white',
                            position: "top-right",
                            hideAfter: 1600,
                            icon: "success",
                            afterHidden: function() {
                                location.reload(true);

                            },
                        });
                    }


                    if (response.payment_status == 0) {

                        $.toast({
                            heading: "Payment Incomplete",
                            text: "Your payment is pending. Please complete the payment to proceed.",
                            icon: "error",
                            loaderBg: "#FFD700", 
                            bgColor: "#EF4444", 
                            textColor: "#FFFFFF", 
                            position: "top-right",
                            hideAfter: 5000, 
                            stack: false, 
                            showHideTransition: "fade", 
                            afterHidden: function() {
                                location.reload(true); 
                            }
                        });

                    }


                },
                error: function(error) {

                }
            });




        });




    });
</script>