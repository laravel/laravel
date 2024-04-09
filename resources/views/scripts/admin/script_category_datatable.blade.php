<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
<script type="text/javascript" src="{{asset('js/validate.js')}}"></script>



<script type="text/javascript">
    $('#edit_btn_category').on('click', function() {

        if ($("#category_edit").valid()) {

            var formData = new FormData($('#category_edit')[0]);


            $.ajax({
                type: "POST",
                url: "/category-edit-submit",
                data: formData,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(response) {

                    if (response.status == 1) {

                        $.toast({
                            heading: "Category Updated",
                            text: "Category has been updated successfully.",
                            bgColor: '#22C55E',
                            loaderBg: '#00ff88',
                            textColor: 'white',
                            position: "top-right",
                            hideAfter: 2100,
                            icon: "success",
                            afterShown: function() {
                                location.reload(true);
                            }
                        });


                    }

                },
                error: function(error) {

                }
            });



        }

    })



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
                    url: "/category-delete-admin",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.delete_category_status == 1) {
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
                    text: 'Your data  is safe :)',
                    icon: 'error',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
            }
        });




    }




    function fnEditClick(id) {

        $.ajax({
            type: "POST",
            url: "/get-category-data-by-id",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {

                if (response.status == 1) {
                    $('#edit_Category_Modal').trigger("reset");

                    $("#category_id_rec_form").val(response.data.id);
                    $("#description_edit").val(response.data.description);
                    $("#CategoryName_edit").val(response.data.name);
                    $("#product_img_edit").attr("src", "/storage/category_images/" + response.data.image);


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



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function category_datatable() {


            var user = "{{ Auth::user()->user_type }}";

            if (user == "super_admin") {

                $('#category_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-category-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'image',
                            name: 'image',
                            render: function(data, type, full, meta) {
                                return '<img src="/storage/category_images/' + data + '" alt="' + data + '" width="100" height="100">';

                            }
                        },

                        {
                            data: 'description',
                            name: 'product_price',

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
                                return `<div class="row " >
                <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#edit_Category_Modal" data-bs-toggle="modal"  ></i>
                 <br>
                 <br>
                <i class="fa fa-trash  delete_product_icon " onclick="fnDeleteClick(${data})" id="${data}"   ></i>
                   
                </div>`;


                            }

                        }

                    ]
                });

            }

            var is_delete = "{{ $data->is_delete }}";
            var is_update = "{{ $data->is_update }}";

            if (user != 'super_admin' && (is_delete == 0 && is_update == 0)) {
                $('#category_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-category-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'image',
                            name: 'image',
                            render: function(data, type, full, meta) {
                                return '<img src="/storage/category_images/' + data + '" alt="' + data + '" width="100" height="100">';

                            }
                        },

                        {
                            data: 'description',
                            name: 'description',

                        },

                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data) {
                                // Return only the first 10 characters
                                return data.substr(0, 10);
                            }
                        },


                    ]
                });
            }

            if (user != 'super_admin' && (is_delete != 0 || is_update != 0)) {

                $('#category_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-category-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'image',
                            name: 'image',
                            render: function(data, type, full, meta) {
                                return '<img src="/storage/category_images/' + data + '" alt="' + data + '" width="100" height="100">';

                            }
                        },

                        {
                            data: 'description',
                            name: 'description',

                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            render: function(data, type, full, meta) {
                                return data.substr(0, 10);
                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {

                                if (is_delete == 1 && is_update == 1) {

                                    return `<div class="row " >
                                    <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#edit_Category_Modal" data-bs-toggle="modal"  ></i>
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
                                    <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#edit_Category_Modal" data-bs-toggle="modal"  ></i>
                                  
                                    
                                    </div>`;
                                }



                            }

                        }

                    ]
                });

            }





        }

        category_datatable();























        $('#category_edit').validate({
            rules: {
                CategoryName_edit: {
                    required: true
                },

                description_edit: {
                    required: true
                }
            },
            messages: {
                CategoryName_edit: {
                    required: "Please enter Category name"
                },

                description_edit: {
                    required: "Please enter Category description"
                }
            }

        });


        $('#category_add').validate({
            rules: {
                CategoryName: {
                    required: true
                },
                image: {
                    required: true,
                    extension: "jpg|png|jpeg"
                },
                description: {
                    required: true
                }
            },
            messages: {
                CategoryName: {
                    required: "Please enter Category name"
                },
                image: {
                    required: "Please select an image",
                    extension: "Please select a valid image file (jpg, png, jpeg)"
                },
                description: {
                    required: "Please enter Category description"
                }
            }

        });



        $('#add_btn_category').on('click', function() {



            if ($("#category_add").valid()) {

                var formData = new FormData($('#category_add')[0]);

                $.ajax({
                    type: "POST",
                    url: "/admin-category-insert",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: "json",
                    success: function(response) {

                        if (response.category_status_insert == 1) {

                            $.toast({
                                heading: "Category Created",
                                text: "New category has been created successfully.",
                                bgColor: '#22C55E',
                                loaderBg: '#00ff88',
                                textColor: 'white',
                                position: "top-right",
                                hideAfter: 2100,
                                icon: "success",
                                afterShown: function() {
                                    location.reload(true);
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