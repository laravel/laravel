<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
<script type="text/javascript" src="{{asset('js/validate.js')}}"></script>



<script type="text/javascript">
    function fnEditClick(id) {

        $.ajax({
            type: "POST",
            url: "/admin-edit-company-form-get-data",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {

                if (response.status == 1) {
                    $('#company_modal_edit').trigger("reset");

                    $("#company_id_edit").val(id);
                    $("#companyName_edit").val(response.data.name);
                    $("#address_edit").val(response.data.address);
                    $("#email_edit").val(response.data.email);
                    $("#phone_edit").val(response.data.phone);
                    $("#country_edit").val(response.data.country);



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
                    url: "/admin-delete-company-record",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.delete_status == 1) {
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






    $(document).ready(function() {






        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });




        $.ajax({
            type: "GET",
            url: "get-category",
            success: function(response) {
                if (response.categories) {
                    var categories = response.categories;
                    var selectOptions = '<option disabled selected> Select Product Category </option>';

                    categories.forEach(function(category) {
                        selectOptions += '<option class="pd_category hover:bg-blue-500 hover:text-white " value="' + category.id + '">' + category.name + '</option>';
                    });

                    $('#productType').append(selectOptions);
                    $('#productType_edit').append(selectOptions);

                }
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });




        function companies_datatable() {

            var user = "{{ Auth::user()->user_type }}";
            var is_delete = "{{ $data->is_delete }}";
            var is_update = "{{ $data->is_update }}";

            if (user == "super_admin") {
                $('#companies_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-companies-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'product_type',
                            name: 'product_type',

                        },

                        {
                            data: 'category_id',
                            name: 'category_id',

                        },
                        {
                            data: 'address',
                            name: 'address',
                            render: function(data, type, full, meta) {
                                return `
                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="${data}">
                                        address
                            </button>`;


                            }

                        },
                        {
                            data: 'email',
                            name: 'email',
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                        },
                        {
                            data: 'country',
                            name: 'country',
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
                <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#company_modal_edit" data-bs-toggle="modal"  ></i>
                 <br>
                 <br>
                <i class="fa fa-trash  delete_product_icon " onclick="fnDeleteClick(${data})" id="${data}"   ></i>
                   
                </div>`;


                            }
                        }

                    ]
                });

            }

            if (user != 'super_admin' && (is_delete == 0 && is_update == 0)) {


                $('#companies_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-companies-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'product_type',
                            name: 'product_type',

                        },

                        {
                            data: 'category_id',
                            name: 'category_id',

                        },
                        {
                            data: 'address',
                            name: 'address',
                            render: function(data, type, full, meta) {
                                return `
                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="${data}">
                                        address
                            </button>`;


                            }

                        },
                        {
                            data: 'email',
                            name: 'email',
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                        },
                        {
                            data: 'country',
                            name: 'country',
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

                $('#companies_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-companies-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'product_type',
                            name: 'product_type',

                        },

                        {
                            data: 'category_id',
                            name: 'category_id',

                        },
                        {
                            data: 'address',
                            name: 'address',
                            render: function(data, type, full, meta) {
                                return `
                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="${data}">
                                        address
                            </button>`;


                            }

                        },
                        {
                            data: 'email',
                            name: 'email',
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                        },
                        {
                            data: 'country',
                            name: 'country',
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
                                if (is_delete == 1 && is_update == 1) {

                                    return `<div class="row " >
                                    <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#company_modal_edit" data-bs-toggle="modal"  ></i>
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

                                    <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#company_modal_edit" data-bs-toggle="modal"  ></i>
                                                                      
                                    </div>`;

                                }



                            }
                        }

                    ]
                });





            }




        }

        companies_datatable();


        $('#add_btn_company').click(function() {
            if ($('#company_add').valid()) {

                var selText = $("#productType option:selected").text();
                var selVal = $("#productType option:selected").val();

                var data = {
                    companyName: $('#companyName').val(),
                    productType: selText,
                    address: $('#address').val(),
                    email: $('#email').val(),
                    phone: $('#phone').val(),
                    country: $('#country').val(),

                    category_id: selVal
                }

                $.ajax({
                    type: "POST",
                    url: "/admin-add-company-form",
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        if (response.status) {

                            $.toast({
                                heading: "Successfully Added",
                                text: "Company Added successfully",
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

        // Initialize form validation
        $('#company_add').validate({
            rules: {
                companyName: {
                    required: true
                },
                productType: {
                    required: true
                },
                address: {
                    required: true
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true
                },
                country: {
                    required: true
                }
            },
            messages: {
                companyName: {
                    required: "Please enter the company name"
                },
                productType: {
                    required: "Please select a product type"
                },
                address: {
                    required: "Please enter the address"
                },
                email: {
                    required: "Please enter your email address",
                    email: "Please enter a valid email address"
                },
                phone: {
                    required: "Please enter your phone number"
                },
                country: {
                    required: "Please enter the country"
                }
            }
        });

        var company_edit_form = $('#company_edit').validate({
            rules: {
                companyName_edit: {
                    required: true
                },

                address_edit: {
                    required: true
                },
                email_edit: {
                    required: true,
                    email: true
                },
                phone_edit: {
                    required: true
                },
                country_edit: {
                    required: true
                }
            },
            messages: {
                companyName_edit: {
                    required: "Please enter the company name"
                },

                address_edit: {
                    required: "Please enter the address"
                },
                email_edit: {
                    required: "Please enter your email address",
                    email: "Please enter a valid email address"
                },
                phone_edit: {
                    required: "Please enter your phone number"
                },
                country_edit: {
                    required: "Please enter the country"
                }
            }
        });



        $('#edit_btn_company').on('click', function() {
            company_edit_form.resetForm();
            if ($("#company_edit").valid()) {

                var selText = $("#productType_edit option:selected").text();
                var selVal = $("#productType_edit option:selected").val();

                var data = {
                    id: $('#company_id_edit').val(),
                    companyName: $('#companyName_edit').val(),
                    productType: selText,
                    address: $('#address_edit').val(),
                    email: $('#email_edit').val(),
                    phone: $('#phone_edit').val(),
                    country: $('#country_edit').val(),
                    category_id: selVal
                }

                $.ajax({
                    type: "POST",
                    url: "/admin-edit-company-form-submit",
                    data: data,
                    dataType: "json",
                    success: function(response) {

                        if (response.status) {

                            $.toast({
                                heading: "Upadted Successfully",
                                text: "Company Details Updated successfully",
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