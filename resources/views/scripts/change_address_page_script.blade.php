<script>
    $(document).ready(function() {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#address_select').select2();

        $("#add_address_btn").on('click', function() {

            window.location.href = "/add-address";


        });




        $("#save_address_btn").on("click", function() {

           var value =  $('#address_select').find(":selected").val();
          
            $.ajax({
                type: "POST",
                url: "/change-address-process",
                data:{value: value},
                dataType: "json",
               
                success: function(response) {

                    if(response.status_changed == 1)
                    {
                        window.location.href = "/cart-checkout-place-order";
                    }


                },

            });



        });


    });
</script>