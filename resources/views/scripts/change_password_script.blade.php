<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>


<script>
    $(document).ready(function() {


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $("#change_password_user").validate({
            rules: {
                current_password: {
                    required: true,
                    minlength: 6
                },
                new_password: {
                    required: true,
                    minlength: 6
                },
                confirm_password: {
                    required: true,
                    minlength: 6,
                    equalTo: "#new_password"
                }
            },
            messages: {
                current_password: {
                    required: "Please enter your current password",
                    minlength: "Your password must be at least 6 characters long"
                },
                new_password: {
                    required: "Please enter a new password",
                    minlength: "Your password must be at least 6 characters long"
                },
                confirm_password: {
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




        $(document).on("click", "#btn_Change_Password", function() {

            if ($("#change_password_user").valid()) {

                var cuurent_password = $("#current_password").val();

                $.ajax({
                    type: "POST",
                    url: "/get-password-status",
                    data: {
                        cuurent_password: cuurent_password
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.current_password_status == 0) {

                            $.toast({
                                heading: "Password Doesn't Match",
                                text: "Enter your current password again",
                                loaderBg: 'yellow',
                                textColor: 'white',
                                position: "top-right",
                                icon: "error",
                                hideAfter: 1900,
                                bgColor: '#FF0000',
                            });

                            $("#current_password").val("");
                            $("#new_password").val("");
                            $("#confirm_password").val("");



                        }

                        if (response.current_password_status == 1) {

                            var password = $("#new_password").val();


                            $.ajax({
                                type: "POST",
                                url: "/change-password-process",
                                data: {
                                    password: password
                                },
                                dataType: "json",
                                success: function(response) {


                                    if (response.change_passowrd_status == 1) {


                                        $.toast({
                                            heading: "Password changed.",
                                            text: "Your password has been changed",
                                            bgColor: '#22C55E',
                                            loaderBg: '#00ff88',
                                            textColor: 'white',
                                            position: "top-right",
                                            icon: "success",
                                            hideAfter: 1500,
                                            afterHidden: function() {
                                                window.location.href = '/user-info-profile';

                                            }
                                        });



                                    }


                                },
                                error: function(error) {

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