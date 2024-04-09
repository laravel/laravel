<script>
    $(document).ready(function() {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#registration').validate({
            rules: {
                first_name: {
                    required: true,
                    minlength: 2,
                },
                last_name: {
                    required: true,
                    minlength: 2,
                },
                email: {
                    required: true,
                },
                phone: {
                    required: true,
                    minlength: 10,
                    maxlength: 10,
                },
                password: {
                    required: true,
                    minlength: 5,
                },
                password_confirmation: {
                    equalTo: "#password"
                },
                pincode: {
                    required: true,
                }
            },
            messages: {
                first_name: {
                    required: "Please enter your first name.",
                    minlength: "Your first name must be at least 2 characters long."
                },
                last_name: {
                    required: "Please enter your last name.",
                    minlength: "Your last name must be at least 2 characters long."
                },
                email: "Please enter your email address.",
                password: {
                    required: "Please enter your password.",
                    minlength: "Your password must be at least 5 characters long."
                },
                phone: {
                    required: "Please enter phone number",
                    minlength: "please enter 10 digit phone number",
                    maxlength: "please enter 10 digit phone number",

                },
                password_confirmation: {
                    equalTo: "Your password and confirmation password do not match."
                },
                pincode: {
                    required: "Please enter your pincode."
                }
            }
        });



        $('#reg_submit').on('click', function() {




            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            if ($("#registration").valid()) {

                var formData = new FormData($('#registration')[0]);
                // var formData = $("#registration").serialize();

                $.ajax({
                    type: "POST",
                    url: "/register-process",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 1) {


                            location.reload(true);
                            $.toast({
                                heading: response.message,
                                text: "Saved.",
                                bgColor: '#22C55E',
                                loaderBg: '#00ff88',
                                textColor: 'white',
                                position: "top-right",
                                icon: "success"
                            });
                            window.location.href = '/home';


                        }
                    },
                    error: function(xhr, status, error) {
                        var errStr = "";

                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            for (var key in xhr.responseJSON.errors) {
                                errStr += xhr.responseJSON.errors[key].join('<br>') + "<br>";
                            }
                        } else {
                            errStr = "An error occurred while processing your request.";
                        }

                        $.toast({
                            heading: 'Fill the Form',
                            text: errStr,
                            loaderBg: 'yellow',
                            textColor: 'white',
                            position: "top-right",
                            icon: "error",
                            bgColor: '#FF0000',
                        });


                    }
                });




            }



        });





    });
</script>