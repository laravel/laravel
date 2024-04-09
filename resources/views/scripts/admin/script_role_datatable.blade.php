<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
<script type="text/javascript" src="{{asset('js/validate.js')}}"></script>




<script type="text/javascript">
    $(document).on("click", ".admin_icon_rights", function() {

        $('#table_rights_form').trigger("reset");
        $("#table_rights_form")[0].reset();

        var id = $(this).attr("id");


        $.ajax({
            type: "POST",
            url: "/get-data-from-role-rights",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {

                if (response.Role_data_status == 1) {



                    $("#role_rights_id").val(id);

                    $("#is_view_category").attr('checked', (response.data[2].is_view == 1) ? true : false);
                    $("#is_update_category").attr('checked', (response.data[2].is_update == 1) ? true : false);
                    $("#is_delete_category").attr('checked', (response.data[2].is_delete == 1) ? true : false);
                    $("#is_created_category").attr('checked', (response.data[2].is_created == 1) ? true : false);

                    $("#is_view_companies").attr('checked', (response.data[1].is_view == 1) ? true : false);
                    $("#is_update_companies").attr('checked', (response.data[1].is_update == 1) ? true : false);
                    $("#is_delete_companies").attr('checked', (response.data[1].is_delete == 1) ? true : false);
                    $("#is_created_companies").attr('checked', (response.data[1].is_created == 1) ? true : false);

                    $("#is_view_products").attr('checked', (response.data[3].is_view == 1) ? true : false);
                    $("#is_update_products").attr('checked', (response.data[3].is_update == 1) ? true : false);
                    $("#is_delete_products").attr('checked', (response.data[3].is_delete == 1) ? true : false);
                    $("#is_created_products").attr('checked', (response.data[3].is_created == 1) ? true : false);

                    $("#is_view_user").attr('checked', (response.data[0].is_view == 1) ? true : false);
                    $("#is_update_user").attr('checked', (response.data[0].is_update == 1) ? true : false);
                    $("#is_delete_user").attr('checked', (response.data[0].is_delete == 1) ? true : false);
                    $("#is_created_user").attr('checked', (response.data[0].is_created == 1) ? true : false);



                } else {
                    $('#table_rights_form').trigger("reset");
                    $("#table_rights_form")[0].reset();


                }

            },
            error: function(error) {

            }
        });


    });


    $(document).ready(function() {


        $("#form_role_new_insert").validate({

            rules: {
                role_name_input: {
                    required: true
                }
            },

            messages: {
                role_name_input: {
                    required: "please enter role name."
                }
            },
            // Set error placement
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function role_datatable() {



            $('#role_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-role-get",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'role',
                        name: 'role'
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
                        data: 'role',
                        name: 'role',
                        render: function(data, type, full, meta) {
                            if (data != 'super_admin' && data != 'user') {
                                return `<i class='fas fa-id-card admin_icon_rights' id="${full.id}" data-bs-target="#rights_modal" data-bs-toggle="modal"  style='font-size:24px'></i>`;
                            } else {
                                return '';
                            }
                        }
                    }
                ]
            });

        }

        role_datatable();


        $(document).on("click", "#form_add_role_save_btn", function() {

            if ($("#form_role_new_insert").valid()) {
                var role_name = $("#role_name_input").val();


                $.ajax({
                    type: "POST",
                    url: "/new-role-insert-submit",
                    data: {
                        role_name: role_name
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.role_created_status == 1) {

                            $.toast({
                                heading: "Role Created",
                                text: " New Role has been created.",
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


        $(document).on("click", "#role_save_btn", function() {


            var data = {

                id: $("#role_rights_id").val(),

                is_view_category: ($('#is_view_category').is(':checked')) ? 1 : 0,
                is_update_category: ($('#is_update_category').is(':checked')) ? 1 : 0,
                is_delete_category: ($('#is_delete_category').is(':checked')) ? 1 : 0,
                is_created_category: ($('#is_created_category').is(':checked')) ? 1 : 0,


                is_view_companies: ($('#is_view_companies').is(':checked')) ? 1 : 0,
                is_update_companies: ($('#is_update_companies').is(':checked')) ? 1 : 0,
                is_delete_companies: ($('#is_delete_companies').is(':checked')) ? 1 : 0,
                is_created_companies: ($('#is_created_companies').is(':checked')) ? 1 : 0,

                is_view_user: ($('#is_view_user').is(':checked')) ? 1 : 0,
                is_update_user: ($('#is_update_user').is(':checked')) ? 1 : 0,
                is_delete_user: ($('#is_delete_user').is(':checked')) ? 1 : 0,
                is_created_user: ($('#is_created_user').is(':checked')) ? 1 : 0,

                is_view_products: ($('#is_view_products').is(':checked')) ? 1 : 0,
                is_update_products: ($('#is_update_products').is(':checked')) ? 1 : 0,
                is_delete_products: ($('#is_delete_products').is(':checked')) ? 1 : 0,
                is_created_products: ($('#is_created_products').is(':checked')) ? 1 : 0,

            }

            $.ajax({
                type: "POST",
                url: "/roles-submit-form",
                data: data,
                dataType: "json",
                success: function(response) {

                    if (response.Roles_changed_status == 1) {

                        $.toast({
                            heading: "Role Rights Updated.",
                            text: "Role Rights has been changed.",
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

        });


    });
</script>