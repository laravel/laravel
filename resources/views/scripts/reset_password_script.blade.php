<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $("#reset_password_form").validate({
            rules: {

                password: {
                    required: true,
                    minlength: 6
                },
                password_confirmation: {
                    required: true,
                    minlength: 6,
                    equalTo: "#password"
                }
            },
            messages: {

                password: {
                    required: "Please enter a new password",
                    minlength: "Your password must be at least 6 characters long"
                },
                password_confirmation: {
                    required: "Please confirm your new password",
                    minlength: "Your password must be at least 6 characters long",
                    equalTo: "Please enter the same password as above"
                }
            },
            submitHandler: function(form) {
                // Here you can handle the form submission. For example, you can send an AJAX request.
                alert("Form submitted successfully!");
                return false; // Prevent the form from submitting normally
            }
        });




        $(document).on("click", "#reset_password_btn", function() {



            if ($("#reset_password_form").valid()) {



                // var email_token = $("#email_token").val();
              

                var data = {
                    password: $("#password").val(),
                    password_confirmation: $("#password_confirmation").val(),
                    email_token: $("#email_token").val()
                }


                $.ajax({
                    type: "POST",
                    url: "/reset-password-process",
                    data:data,
                    dataType: "json",
                    success: function(response) {

                        if (response.password_status == 1) {

                            $.toast({
                                heading: "Password Reset Successfully.",
                                text: "Your password has been reset",
                                bgColor: '#22C55E',
                                loaderBg: '#00ff88',
                                textColor: 'white',
                                position: "top-right",
                                icon: "success",
                                hideAfter: 1500,
                                afterHidden: function() {
                                    window.location.href = '/login';


                                }
                            });



                        }

                    },
                    error: function(error) {

                    }
                });


            }





        });








    });
</script>