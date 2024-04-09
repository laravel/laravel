<script>
    $(document).ready(function() {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $('#address_insert').validate({
            rules: {
                address: {
                    required: true
                },
                city: {
                    required: true
                },
                country: {
                    required: true
                },
                pincode: {
                    required: true
                }
            },
            messages: {
                address: "Please enter your address",
                city: "Please enter your city",
                country: "Please enter your country",
                pincode: "Please enter your postal code"
            }
        });


        $('#address_insert_btn').on('click', function() {

            var formData = new FormData($('#address_insert')[0]);

            if ($("#address_insert").valid()) {

                $.ajax({
                    type: "POST",
                    url: "/add-address-process",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false, 
                    success: function(response) {
                        if (response.status == 1) {

                            $.toast({
                                heading: "Address added",
                                text: "New address added.",
                                bgColor: '#3B82F6',
                                loaderBg: '#60A5FA',
                                textColor: 'white',
                                position: "top-right",
                                icon: "success"
                            });

                            window.location.href ="/get-data-address";
                        }
                    },

                });
            }
        });





    });
</script>