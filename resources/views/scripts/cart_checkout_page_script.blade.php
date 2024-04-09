<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $("#place_order_btn").on('click', function() {

            window.location.href = "/cart-checkout-place-order";


        });





        $(".cart_increment_item").on("click", function() {
            // alert("Increment");
            var str = $(this).attr("id");
            var temp_str = str.split("_");
            var id = temp_str[1];

            $.ajax({
                type: "POST",
                url: "/cart-checkout-increment",
                data: {
                    id: id,
                },
                dataType: "json",
                success: function(response) {

                    if(response.out_of_stock == 1)
                    {
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

                    if (response.status_increment == 1) {

                        var quantity_tag = ("#quantity_") + id;
                        var discount_tag = ("#discount_") + id;
                        var price_tag = ("#price_") + id;
                        var text_quantity = ("#text_quantity_") + id;


                        $(quantity_tag).text("Quantity :" + response.cart_items.quantity);
                        $(discount_tag).text("Discount: " + response.cart_items.discount_amount);
                        $(price_tag).text("Price: " + response.cart_items.total_price);


                        $(text_quantity).val(response.cart_items.quantity);

                        $("#main_quantity").text(response.cart.number_of_items);
                        $("#main_discount_amount").text(response.cart.discount_amount);
                        $("#main_price").text(response.cart.price);
                        $("#main_sub_total_price").text(response.cart.sub_total);

                        $('#cart_items_count_navbar_icon').text(response.cart.number_of_items);





                        $.toast({
                            heading: response.head,
                            text: response.message,
                            bgColor: '#3B82F6',
                            loaderBg: '#60A5FA',
                            textColor: 'white',
                            position: "top-right",
                            icon: "success"
                        });



                    }



                },

            });


        })

        $(".cart_decrement_item").on("click", function() {

            var str = $(this).attr("id");
            var temp_str = str.split("_");
            var id = temp_str[1];

            $.ajax({
                type: "POST",
                url: "/cart-checkout-decrement",
                data: {
                    id: id,
                },
                dataType: "json",
                success: function(response) {

                    if (response.status_decrement == 1) {

                        var quantity_tag = ("#quantity_") + id;
                        var discount_tag = ("#discount_") + id;
                        var price_tag = ("#price_") + id;
                        var text_quantity = ("#text_quantity_") + id;




                        $(quantity_tag).text("Quantity :" + response.cart_items.quantity);
                        $(discount_tag).text("Discount: " + response.cart_items.discount_amount);
                        $(price_tag).text("Price: " + response.cart_items.total_price);

                        $(text_quantity).val(response.cart_items.quantity);


                        $("#main_quantity").text(response.cart.number_of_items);
                        $("#main_discount_amount").text(response.cart.discount_amount);
                        $("#main_price").text(response.cart.price);
                        $("#main_sub_total_price").text(response.cart.sub_total);

                        $('#cart_items_count_navbar_icon').text(response.cart.number_of_items);




                        $.toast({
                            heading: response.head,
                            text: response.message,
                            bgColor: '#3B82F6',
                            loaderBg: '#60A5FA',
                            textColor: 'white',
                            position: "top-right",
                            icon: "success"
                        });

                    }

                    if (response.status_decrement == 0) {




                        const swalWithBootstrapButtons = Swal.mixin({
                            customClass: {
                                confirmButton: "btn btn-success",
                                cancelButton: "btn btn-danger"
                            },
                            buttonsStyling: true
                        });

                        swalWithBootstrapButtons.fire({
                            title: "Are you sure want to delete this item?",
                            text: "You won't be able to revert this!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonText: "Yes, delete it!",
                            cancelButtonText: "No, cancel!",
                            reverseButtons: true
                        }).then((result) => {
                            if (result.isConfirmed) {

                                $.ajax({
                                    url: '/cart-checkout-item-delete',
                                    type: 'POST',
                                    data: {
                                        id: id
                                    },
                                    success: function(response) {



                                        if (response.status_deleted == 1) {

                                            swalWithBootstrapButtons.fire({
                                                title: "Deleted!",
                                                text: "Your item  has been deleted.",
                                                icon: "success"
                                            });

                                            $("#main_quantity").text(response.cart.number_of_items);
                                            $("#main_discount_amount").text(response.cart.discount_amount);
                                            $("#main_total_price").text(response.cart.price);
                                            $('#cart_items_count_navbar_icon').text(response.cart.number_of_items);

                                            $("#item_container_" + id).remove();

                                            $.toast({
                                                heading: "Empty Cart.",
                                                text: "Added item to shop...",
                                                bgColor: '#3B82F6',
                                                loaderBg: '#60A5FA',
                                                textColor: 'white',
                                                position: "top-right",
                                                icon: "success",
                                                hideAfter: 4500,
                                                afterShown: function() {
                                                    window.location.href = "/products";
                                                }
                                            });




                                        }


                                    },
                                    error: function(xhr, status, error) {
                                        swalWithBootstrapButtons.fire({
                                            title: "Error",
                                            text: "An error occurred while deleting the file.",
                                            icon: "error"
                                        });
                                    }
                                });
                            } else if (result.dismiss === Swal.DismissReason.cancel) {
                                swalWithBootstrapButtons.fire({
                                    title: "Cancelled",
                                    text: "Your imaginary file is safe :)",
                                    icon: "error"
                                });
                            }
                        });











                    }



                },

            });

        })



    });
</script>