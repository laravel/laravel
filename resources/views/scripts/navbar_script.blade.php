<script>
    $(document).ready(function() {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#navbar_search_btn ').on('click', function() {

            
            var searchQuery = $("#navbar_search_box").val();


            $.ajax({
                type: "POST",
                url: "/navbar-search-query",
                data: {
                    searchQuery: searchQuery
                },
                dataType: "json",
                success: function(response) {

                    if(response.status_products_search_navbar == 1){
                        
                        window.location.href = "/products-search";

                    }
                


                },
                error: function(error) {

                }
            });



        })



        $('#cart_Anchor ').on('click', function() {

            if ($("#cart_items_count_navbar_icon").text() == "0") {
                $.toast({
                    heading: "Please Add item to cart",
                    text: "Atleast one item.",
                    bgColor: '#EF4444',
                    loaderBg: '#F87171',
                    textColor: 'white',
                    position: "top-right",
                    icon: "info"
                });
            }

        });












        // $.ajax({
        //     type: "GET",
        //     url: "/Login-check",

        //     dataType: "json",
        //     success: function(response) {




        //         $('#cart_items_count_navbar_icon').text(response.number_of_items_navbar);

        //         if (response.number_of_items_navbar != 0) {
        //             $('#cart_Anchor').attr('href', '/cart-checkout');
        //         }



        //     },

        // });




    });
</script>