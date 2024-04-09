<script>
    $(document).ready(function() {




        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });





        $('#login_form').validate({
            rules: {
                email_or_phone: {
                    required: true
                },
                password: {
                    required: true
                }
            },
            messages: {
                email_or_phone: {
                    required: "please enter email or phone number."
                },
                password: {
                    required: "please enter password."
                }
            }


        });



        $(document).on('click', '#btn_login', function(e) {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var data = {
                "email_or_phone": $('#email_or_phone').val(),
                "password": $('#password').val()
            }


            if ($("#login_form").valid()) {

                $.ajax({
                    type: "POST",
                    url: "login-process",
                    data: data,
                    dataType: "json",
                    success: function(response) {


                        if (response.admin_status_login == 1) {
                            window.location.href = "/admin-dashboard";
                        }

                        if (response.status == 1) {
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
                        if(response.status == 0){

                            $.toast({
                                heading: response.head,
                                text: response.message,
                                loaderBg: 'yellow',
                                textColor: 'white',
                                position: "top-right",
                                icon: "error",
                                bgColor: '#FF0000',
                            });
                        }

                       


                    },
                    error: function(response) {



                        var errStr = "";

                        if (response.responseJSON && response.responseJSON.errors) {
                            console.log(response.responseJSON.errors);
                            for (var key in response.responseJSON.errors) {
                                errStr += response.responseJSON.errors[key].join('<br>') + "<br>";
                            }

                            $.toast({
                                heading: 'Fill the Form',
                                text: errStr,
                                loaderBg: 'yellow',
                                textColor: 'white',
                                position: "top-right",
                                icon: "error",
                                bgColor: '#f21b3b',
                            });
                        }




                    }
                });

            }











        })









    });
</script>