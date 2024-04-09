<script>
    $(document).ready(function() {


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
                }
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });

        $('#category').select2();
        $('#company').select2();


                
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

                    console.log(response);

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



        $('#product_add').validate({
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
                category:{
                    required:true,
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
                category:{
                    required: "Please enter the category."
                }
            },
            errorPlacement: function(error, element) {
                // Customize error placement here
                error.insertAfter(element); // Insert error message after the input field
                error.addClass('text-red-500 text-sm  font-semibold'); // Add Tailwind CSS classes to error message
                element.addClass('border-red-500'); // Add Tailwind CSS class to highlight input field
            }

        });



        $('#add_btn').on('click', function() {




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