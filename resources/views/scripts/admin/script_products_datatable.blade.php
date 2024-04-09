<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>
<script type="text/javascript" src="{{asset('js/validate.js')}}"></script>


<script type="text/javascript">
    function fnEditClick(id) {

        $.ajax({
            type: "POST",
            url: "/get-product-data-editForm",
            data: {
                id: id
            },
            dataType: "json",
            success: function(response) {

                if (response.status == 1) {
                    $('#product_edit_modal').trigger("reset");

                    $("#product_id_edit").val(id);
                    $("#productName_edit").val(response.data.name);
                    $("#category_edit").val(response.data.category_id);
                    $("#color_edit").val(response.data.color);
                    $("#weight_edit").val(response.data.weight);
                    $("#quantity_edit").val(response.data.stock_quantity);
                    $("#price_edit").val(response.data.price);
                    $("#discount_edit").val(response.data.discount_amount);
                    $("#description_edit").val(response.data.description);
                    $("#product_img_edit").attr('src', '/storage/product_images/' + response.data.product_image);

                    // id="product_img_edit"

                    var selectedVal = $("#category_edit").find(':selected').val();
                    var selectedText = $("#category_edit").find(':selected').text();

                    $.ajax({
                        type: "POST",
                        url: "/get-company",
                        data: {
                            selectedText: selectedText,
                            id: selectedVal
                        },
                        success: function(response) {
                            var selectElement = $('#company_edit');
                            selectElement.empty();


                            if (response.companies.length === 0) {} else {

                                $.each(response.companies, function(index, item) {
                                    var option = $('<option>', {
                                        value: item.id,
                                        text: item.name
                                    });
                                    selectElement.append(option);
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error:", error);
                        }
                    });

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


    $('#edit_btn_product').on('click', function() {
        if ($("#product_edit_form").valid()) {



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Using class selector to select the form
            var formData = new FormData($('#product_edit_form')[0]);


            $.ajax({
                type: "POST",
                url: "/submit-product-data-editForm",
                data: formData,
                processData: false, // Important! Required for sending FormData object
                contentType: false, // Important! Required for sending FormData object
                success: function(response) {
                    if (response.status == 1) {
                        $.toast({
                            heading: "Details Updated..",
                            text: "Saved.",
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
                        bgColor: '#FF0000'
                    });
                }
            });
        }
    });



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
                    url: "/delete-product-table",
                    data: {
                        id: id
                    },
                    dataType: "json",
                    success: function(response) {

                        if (response.product_delete_status == 1) {
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


        function products_datatable() {

            var user = "{{ Auth::user()->user_type }}";

            if (user == "super_admin") {

                $('#products_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-products-get",

                    columns: [{
                            data: 'id',
                            name: 'id'
                        },


                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'company_name',
                            name: 'company_name',


                        },

                        {
                            data: 'category_name',
                            name: 'category_name',
                        },
                        {
                            data: 'color',
                            name: 'color'
                        },
                        {
                            data: 'weight',
                            name: 'weight'
                        },
                        {
                            data: 'stock_quantity',
                            name: 'stock_quantity'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'discount_amount',
                            name: 'discount_amount'
                        },
                        {
                            data: 'discount_percent',
                            name: 'discount_percent'
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

                                return `<a href="/product-view-admin/${full.id}"  ><i class="fa fa-eye eye_icon_order"  id="${full.id}" data-bs-target="#order_modal" data-bs-toggle="modal" aria-hidden="true"></i></a>`;

                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {
                                return `<div class="row " >
                <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#product_edit_modal" data-bs-toggle="modal"  ></i>
                 <br>
                 <br>
                <i class="fa fa-trash  delete_product_icon " onclick="fnDeleteClick(${data})" id="${data}"   ></i>
                   
                </div>`;

                            }

                        },


                    ],
                    "columnDefs": [{
                        "orderable": false,
                        "targets": 12
                    }],
                });
            }


            var is_delete = "{{ $data->is_delete }}";
            var is_update = "{{ $data->is_update }}";

            if (user != 'super_admin' && (is_delete == 0 && is_update == 0)) {


                var is_delete = "{{ $data->is_delete }}";
                var is_update = "{{ $data->is_update }}";

                $('#products_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-products-get",

                    columns: [{
                            data: 'id',
                            name: 'id'
                        },


                        {
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'company_name',
                            name: 'company_name',


                        },

                        {
                            data: 'category_name',
                            name: 'category_name',
                        },
                        {
                            data: 'color',
                            name: 'color'
                        },
                        {
                            data: 'weight',
                            name: 'weight'
                        },
                        {
                            data: 'stock_quantity',
                            name: 'stock_quantity'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'discount_amount',
                            name: 'discount_amount'
                        },
                        {
                            data: 'discount_percent',
                            name: 'discount_percent'
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

                                return `<a href="/product-view-admin/${full.id}"  ><i class="fa fa-eye eye_icon_order"  id="${full.id}" data-bs-target="#order_modal" data-bs-toggle="modal" aria-hidden="true"></i></a>`;

                            }
                        },



                    ],
                    "columnDefs": [{
                        "orderable": false,
                        "targets": 12
                    }],
                });




            }

            if (user != 'super_admin' && (is_delete != 0 || is_update != 0)) {


                $('#products_datatable').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "/admin-datatable-products-get",
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'company_name',
                            name: 'company_name',
                        },
                        {
                            data: 'category_name',
                            name: 'category_name',
                        },
                        {
                            data: 'color',
                            name: 'color'
                        },
                        {
                            data: 'weight',
                            name: 'weight'
                        },
                        {
                            data: 'stock_quantity',
                            name: 'stock_quantity'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'price',
                            name: 'price'
                        },
                        {
                            data: 'discount_amount',
                            name: 'discount_amount'
                        },
                        {
                            data: 'discount_percent',
                            name: 'discount_percent'
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

                                return `<a href="/product-view-admin/${full.id}"  ><i class="fa fa-eye eye_icon_order"  id="${full.id}" data-bs-target="#order_modal" data-bs-toggle="modal" aria-hidden="true"></i></a>`;

                            }
                        },
                        {
                            data: 'id',
                            name: 'id',
                            render: function(data, type, full, meta) {
                                if (is_delete == 1 && is_update == 1) {

                                    return `<div class="row " >
                                            <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#product_edit_modal" data-bs-toggle="modal"  ></i>
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
                                                <i class="fas fa-edit edit_product_icon " id="${data}"  onclick="fnEditClick(${data})"  data-bs-target="#product_edit_modal" data-bs-toggle="modal"  ></i>
                                            </div>`;
                                }
                            }
                        }

                    ],
                    "columnDefs": [{
                        "orderable": false,
                        "targets": 12
                    }],
                });




            }





        }

        products_datatable();

        $.ajax({
            type: "GET",
            url: "get-category",
            success: function(response) {
                if (response.categories) {
                    var categories = response.categories;
                    var selectOptions = '<option> </option>';

                    categories.forEach(function(category) {
                        selectOptions += '<option class="pd_category hover:bg-blue-500 hover:text-white " value="' + category.id + '">' + category.name + '</option>';
                    });

                    $('#category').append(selectOptions);
                    $('#category_edit').append(selectOptions);

                }
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });


        $("#category_edit").change(function() {



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var selectedVal = $(this).find(':selected').val();
            var selectedText = $(this).find(':selected').text();

            $.ajax({
                type: "POST",
                url: "/get-company",
                data: {
                    selectedText: selectedText,
                    id: selectedVal
                },
                success: function(response) {
                    var selectElement = $('#company_edit');
                    selectElement.empty();


                    if (response.companies.length === 0) {} else {

                        $.each(response.companies, function(index, item) {
                            var option = $('<option>', {
                                value: item.id,
                                text: item.name
                            });
                            selectElement.append(option);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });




        });





        $("#category").change(function() {



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            var selectedVal = $(this).find(':selected').val();
            var selectedText = $(this).find(':selected').text();


            $.ajax({
                type: "POST",
                url: "/get-company",
                data: {
                    selectedText: selectedText,
                    id: selectedVal
                },
                success: function(response) {
                    var selectElement = $('#company');
                    selectElement.empty();


                    if (response.companies.length === 0) {} else {

                        $.each(response.companies, function(index, item) {
                            var option = $('<option>', {
                                value: item.id,
                                text: item.name
                            });
                            selectElement.append(option);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });




        });

        $.validator.addMethod("checkDiscount", function(value, element, params) {

            var price = parseFloat($("price").val());
            var discount = parseFloat($("discount").val());

            return discount < price || discount == price;

        }, "Discount cannot be greater than or equal to the price.");





        var add_product_form = $('#product_add').validate({
            rules: {
                productName: {
                    required: true,

                },
                company: {
                    required: true,

                },
                productImage: {
                    required: true,

                },
                color: {
                    required: true,
                },
                weight: {
                    required: true,

                },
                quantity: {
                    required: true,

                },
                description: {
                    required: true,

                },
                price: {
                    required: true,

                },
                category: {
                    required: true,
                },
                discount: {
                    checkDiscount: true,
                }

            },
            messages: {
                productName: {
                    required: "Please enter the product name.",

                },
                company: {
                    required: "Please enter the company name.",

                },
                productImage: {
                    required: "Please select an image.",

                },
                color: {
                    required: "please enter the color."
                },
                weight: {
                    required: "please enter the weight."
                },
                quantity: {
                    required: "Please enter the quantity.",
                },
                description: {
                    required: "please enter the description"
                },
                price: {
                    required: "Please enter the price.",

                },
                category: {
                    required: "Please enter the category."
                },
                discount: {
                    checkDiscount: "Discount cannot be greater or equal than the price.",
                }

            },
            errorPlacement: function(error, element) {
                // Customize error placement here
                error.insertAfter(element); // Insert error message after the input field
                error.addClass('text-red-500 text-sm  font-semibold'); // Add Tailwind CSS classes to error message
                element.addClass('border  border-danger'); // Add Tailwind CSS class to highlight input field
            }

        });


        $('#product_edit_form').validate({
            rules: {
                productName: {
                    required: true,

                },
                company: {
                    required: true,

                },
                color: {
                    required: true,
                },
                weight: {
                    required: true,

                },
                quantity: {
                    required: true,

                },
                description: {
                    required: true,

                },
                price: {
                    required: true,

                },
                category: {
                    required: true,
                },

            },
            messages: {
                productName: {
                    required: "Please enter the product name.",

                },
                company: {
                    required: "Please enter the company name.",

                },
                color: {
                    required: "please enter the color."
                },
                weight: {
                    required: "please enter the weight."
                },
                quantity: {
                    required: "Please enter the quantity.",
                },
                description: {
                    required: "please enter the description"
                },
                price: {
                    required: "Please enter the price.",

                },
                category: {
                    required: "Please enter the category."
                },

            },
            errorPlacement: function(error, element) {
                // Customize error placement here
                error.insertAfter(element); // Insert error message after the input field
                error.addClass('text-danger text-sm  font-semibold'); // Add Tailwind CSS classes to error message
                element.addClass('border  border-danger'); // Add Tailwind CSS class to highlight input field
            }

        });




        $('#add_product_modal_btn').on('click', function() {
            add_product_form.resetForm();
            $('#product_add').trigger("reset");
            $('#product_add input, #product_add select, #product_add textarea,product_add option').removeClass('border-danger').removeClass('text-danger').removeClass('text-sm');


        })







        $('#add_btn_product').on('click', function() {



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });



            if ($("#product_add").valid()) {

                var formData = new FormData($('#product_add')[0]);
                // var formData = $("#registration").serialize();

                $.ajax({
                    type: "POST",
                    url: "/Add-Product-Process",
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 1) {

                            $.toast({
                                heading: response.message,
                                text: "Saved.",
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



        });







    });
</script>