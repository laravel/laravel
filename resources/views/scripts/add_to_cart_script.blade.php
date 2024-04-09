<script>
    $(document).ready(function() {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $(document).on('click', '.add_to_cart', function() {
            var id = $(this).attr("id");
            var quantity = $(this).attr("quantity");

            $.ajax({
                type: "POST",
                url: "/add-to-cart",
                data: {
                    id: id,
                    quantity: quantity,
                },
                dataType: "json",
                success: function(response) {

                    if (response.status == 0 || response.status_login == 0) {
                        $.toast({
                            heading: 'Login or Register',
                            text: response.message,
                            loaderBg: 'yellow',
                            textColor: 'white',
                            hideAfter: 1700,
                            position: "top-right",
                            icon: "error",
                            bgColor: '#FF0000',
                        });

                        setTimeout(function() {
                            window.location.href = "/login";
                        }, 1700); // Delay for 5000 milliseconds (5 seconds)

                    }
                    if (response.status == 1) {

                        $.toast({
                            heading: response.head,
                            text: response.message,
                            bgColor: '#3B82F6',
                            loaderBg: '#60A5FA',
                            textColor: 'white',
                            position: "top-right",
                            icon: "success"
                        });


                        $('#cart_items_count_navbar_icon').text(response.total_items);
                        $('#cart_Anchor').attr('href', '/cart-checkout');


                    }

                    if (response.out_of_stock == 1) {


                        $.toast({
                            heading: "Sorry",
                            text: "We've reached our stock limit for this product.",
                            bgColor: '#3B82F6',
                            loaderBg: '#60A5FA',
                            textColor: 'white',
                            position: "top-right",
                            icon: "info"
                        });
                    }

                    if (response.products_out_of_stock == 1) {

                        $.toast({
                        heading: "Out of Stock",
                        text: "Try again after few days...",
                        bgColor: '#3B82F6',
                        loaderBg: '#60A5FA',
                        textColor: 'white',
                        position: "top-right",
                        icon: "info"
                    });

                    }

                    
                },
                error: function(error) {

                }
            });


        });








    });
</script>