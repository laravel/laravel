<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $("#emailForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                email: {
                    required: "Please enter your email address.",
                    email: "Please enter a valid email address."
                }
            }

        });


        $(document).on("click", "#forgot_password_btn", function() {



            if ($("#emailForm").valid()) {


                var loader = `
            <i  class="fa fa-circle-notch fa-spin  loader_icon mr-2"></i> Loading`;


                $("#span_btn_forgot").empty();

                $("#span_btn_forgot").append(loader);

                var email = $("#email").val();

                $.ajax({
                    type: "POST",
                    url: "/check-email-process",
                    data: {
                        email: email
                    },
                    dataType: "json",
                    success: function(response) {


                        if (response.status_found_email == 0) {
                            $("#span_btn_forgot").empty();

                            $("#span_btn_forgot").text("User Not Found.");

                            $.toast({
                                heading: "User doesn't exist.",
                                text: "User not found with given email.",
                                loaderBg: 'yellow',
                                textColor: 'white',
                                position: "top-right",
                                icon: "error",
                                hideAfter: 1900,
                                bgColor: '#FF0000',
                            });


                        }

                        if (response.status_found_email == 1) {

                            $("#span_btn_forgot").empty();

                            $("#span_btn_forgot").text("Email Sent.");

                            $.toast({
                                heading: "Email Sent",
                                text: "Your email has been sent",
                                bgColor: '#22C55E',
                                loaderBg: '#00ff88',
                                textColor: 'white',
                                position: "top-right",
                                icon: "success",
                                hideAfter: 1500,

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