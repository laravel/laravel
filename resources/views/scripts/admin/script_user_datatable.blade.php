<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
<script type="text/javascript" src="{{asset('js/validate.js')}}"></script>





<script type="text/javascript">
    function fnDeleteClick(id) {


        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
            customClass: {
                confirmButton: 'btn btn-primary me-1',
                cancelButton: 'btn btn-label-secondary'
            },
            buttonsStyling: false
        }).then(function(result) {
            if (result.value) {


                $.ajax({
                    type: "POST",
                    url: "/user-delete-datatable",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.user_delete_status == 1) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Your record has been deleted.',
                                customClass: {
                                    confirmButton: 'btn btn-success'
                                }
                            });


                            setTimeout(function() {
                                location.reload(true);

                            }, 1500);



                        } else {

                            Swal.fire({
                                title: 'Error!',
                                text: 'something went wrong',
                                type: 'error',
                                customClass: {
                                    confirmButton: 'btn btn-primary'
                                },
                                buttonsStyling: false
                            })


                        }




                    },
                    error: function(error) {

                    }
                });


            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire({
                    title: 'Cancelled',
                    text: 'Your imaginary file is safe :)',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
            }
        });




    }














    function get_dropdwon_data() {


        $.ajax({
            type: "GET",
            url: "/get-roles-data-dropdown",

            success: function(response) {
                var selectElement = $('#user_type_select');
                var selectElement_edit = $('#user_type_select_edit');

                selectElement.empty();
                selectElement_edit.empty();

                if (response.roles.length === 0) {} else {

                    $.each(response.roles, function(index, item) {
                        var option = $('<option>', {
                            value: item.id,
                            text: item.role
                        });

                        var option1 = $('<option>', {
                            value: item.id,
                            text: item.role
                        });

                        selectElement.append(option);
                        selectElement_edit.append(option1);
                    });
                }

            },
            error: function(xhr, status, error) {
                console.error("Error:", error);
            }
        });
    }

    get_dropdwon_data();







    function fnEditClick(id) {

        $.ajax({
            type: "POST",
            url: "/user-get-data-by-id",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {

                if (response.status == 1) {
                    $('#user_edit_form').trigger("reset");

                    $("#user_edit_id_datatable").val(id);
                    $("#first_name_edit").val(response.data.first_name);
                    $("#last_name_edit").val(response.data.last_name);
                    $("#user_type_select_edit").val(response.data.role_id);
                    $("#email_edit").val(response.data.email);
                    $("#phone_edit").val(response.data.phone);



                } else {

                    Swal.fire({
                        title: 'Something went wrong',
                        text: ' Try again ',
                        type: 'error',
                        customClass: {
                            confirmButton: 'btn btn-primary'
                        },
                        buttonsStyling: false
                    })


                }


            },
            error: function(error) {

            }
        });

    }











    $(document).ready(function() {

        function user_datatable() {

            var user = "{{ Auth::user()->user_type }}";
            var is_delete = "{{ $data->is_delete }}";
            var is_update = "{{ $data->is_update }}";

            if (user == "super_admin") {

                $('#user_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-user-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },


                        {
                            data: 'first_name',
                            name: 'first_name'
                        },

                        {
                            data: 'last_name',
                            name: 'last_name',
                        },
                        {
                            data: 'full_name',
                            name: 'full_name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },


                        {
                            data: 'role_id',
                            name: 'role_id'
                        },
                        {
                            data: 'user_type',
                            name: 'user_type'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },

                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data) {
                                // Return only the first 10 characters
                                return data.substr(0, 10);
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {
                                return `<a  href="/admin-user-view/${full.id}" > <i class="fa fa-eye eye_icon_user" id="${full.id}" aria-hidden="true"></i> </a>`;

                            }

                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {
                                return `<div class="row " >
                            <i class="fas fa-edit edit_product_icon edit_category_icon" id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#user_modal_admin_edit" data-bs-toggle="modal"  ></i>
                            <br>
                            <br>
                            <i class="fa fa-trash  delete_product_icon " onclick="fnDeleteClick(${data})" id="${data}"   ></i>
                            
                            </div>`;


                            }

                        },


                    ]
                });
            }

            if (user != 'super_admin' && (is_delete == 0 && is_update == 0)) {

                $('#user_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-user-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },


                        {
                            data: 'first_name',
                            name: 'first_name'
                        },

                        {
                            data: 'last_name',
                            name: 'last_name',
                        },
                        {
                            data: 'full_name',
                            name: 'full_name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },


                        {
                            data: 'role_id',
                            name: 'role_id'
                        },
                        {
                            data: 'user_type',
                            name: 'user_type'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },

                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data) {
                                // Return only the first 10 characters
                                return data.substr(0, 10);
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {
                                return `<a  href="/admin-user-view/${full.id}" > <i class="fa fa-eye eye_icon_user" id="${full.id}" aria-hidden="true"></i> </a>`;


                            }

                        }

                    ]
                });
            }

            if (user != 'super_admin' && (is_delete != 0 || is_update != 0)) {

                $('#user_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-user-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'first_name',
                            name: 'first_name'
                        },
                        {
                            data: 'last_name',
                            name: 'last_name',
                        },
                        {
                            data: 'full_name',
                            name: 'full_name'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'role_id',
                            name: 'role_id'
                        },
                        {
                            data: 'user_type',
                            name: 'user_type'
                        },
                        {
                            data: 'phone',
                            name: 'phone'
                        },

                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data) {
                                // Return only the first 10 characters
                                return data.substr(0, 10);
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {
                                return `<a  href="/admin-user-view/${full.id}" > <i class="fa fa-eye eye_icon_user" id="${full.id}" aria-hidden="true"></i> </a>`;


                            }

                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {

                                if (is_delete == 1 && is_update == 1) {
                                    return `<div class="row " >
                                    <i class="fas fa-edit edit_product_icon edit_category_icon" id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#user_modal_admin_edit" data-bs-toggle="modal"  ></i>
                                    <br>
                                    <br>
                                    <i class="fa fa-trash  delete_product_icon " onclick="fnDeleteClick(${data})" id="${data}"   ></i>
                                    
                                    </div>`;
                                } else if (is_delete == 1 && is_update == 0) {

                                    return `<div class="row " >
                                 
                                    <i class="fa fa-trash  delete_product_icon " onclick="fnDeleteClick(${data})" id="${data}"   ></i>
                                    
                                    </div>`;
                                } else if (is_delete == 0 && is_update == 1) {
                                    return `<div class="row " >
                                   
                                    <i class="fas fa-edit edit_product_icon edit_category_icon" id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#user_modal_admin_edit" data-bs-toggle="modal"  ></i>

                                    
                                    </div>`;
                                }







                            }

                        },


                    ]
                });


            }





        }

        user_datatable();






        $(document).on("click", ".edit_category_icon", function() {
            $('#user_edit_form').trigger("reset");

        });


        $(document).on("click", "#open_modal_user", function() {
            $('#user_insert_form').trigger("reset");
        });






        $('#user_insert_form').validate({
            rules: {
                first_name: {
                    required: true
                },
                last_name: {
                    required: true
                },
                user_type_select: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true
                },
                password: {
                    required: true
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
                },
                city: {
                    required: true
                },
                state: {
                    required: true
                },
                country: {
                    required: true
                },
                description: {
                    required: true
                },
                pincode: {
                    required: true
                }
            },
            messages: {
                first_name: {
                    required: "Please enter first name"
                },
                last_name: {
                    required: "Please enter last name"
                },
                user_type_select: {
                    required: "Please select a role"
                },
                email: {
                    required: "Please enter email address",
                    email: "Please enter a valid email address"
                },
                phone: {
                    required: "Please enter phone number"
                },
                password: {
                    required: "Please enter  password"
                },
                password_confirmation: {
                    required: "Please enter confirm password",
                    equalTo: "Please again enter same password"
                },
                city: {
                    required: "Please enter city"
                },
                state: {
                    required: "Please enter state"
                },
                country: {
                    required: "Please enter country"
                },
                description: {
                    required: "Please enter address"
                },
                pincode: {
                    required: "Please enter Pincode"

                }
            },

        });


        $('#add_btn_user').on('click', function() {

            if ($("#user_insert_form").valid()) {



                var formData = new FormData($('#user_insert_form')[0]);

                $.ajax({
                    type: "POST",
                    url: "/user-created-admin",
                    data: formData,
                    dataType: "json",
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        if (response.user_created_status == 1) {

                            $.toast({
                                heading: "User Created.",
                                text: "New user created successfully.",
                                bgColor: '#22C55E',
                                loaderBg: '#00ff88',
                                textColor: 'white',
                                position: "top-right",
                                hideAfter: 2100,
                                icon: "success",
                                afterShown: function() {
                                    location.reload(true);

                                },
                            });

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

        })


        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });



        $('#user_edit_form').validate({
            rules: {
                first_name_edit: {
                    required: true
                },
                last_name_edit: {
                    required: true
                },
                user_type_select_edit: {
                    required: true
                },
                email_edit: {
                    required: true,
                    email: true
                },
                phone_edit: {
                    required: true
                },
                password_confirmation_edit: {

                    equalTo: "#password_edit"
                },

            },
            messages: {
                first_name_edit: {
                    required: "Please enter first name"
                },
                last_name_edit: {
                    required: "Please enter last name"
                },
                user_type_select_edit: {
                    required: "Please select a role"
                },
                email_edit: {
                    required: "Please enter email address",
                    email: "Please enter a valid email address"
                },
                phone_edit: {
                    required: "Please enter phone number"
                },
                password_confirmation_edit: {

                    equalTo: "password and confirm password should be same."
                },

            },

        });



        $('#edit_btn_use_formr').on('click', function() {
            if ($("#user_edit_form").valid()) {

                var formData = new FormData($('#user_edit_form')[0]);


                $.ajax({
                    type: "POST",
                    url: "/user-edit-details-datatable",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response) {

                        if (response.user_edit_status) {

                            $.toast({
                                heading: "Updated.",
                                text: "user details upadted.",
                                bgColor: '#22C55E',
                                loaderBg: '#00ff88',
                                textColor: 'white',
                                position: "top-right",
                                hideAfter: 2100,
                                icon: "success",
                                afterShown: function() {
                                    location.reload(true);

                                },
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