<script type="text/javascript" src="{{asset('js/toast.js')}}"></script>


<script type="text/javascript" src="{{asset('js/select.js')}}"></script>




<script type="text/javascript">
    document.querySelector('.btn').addEventListener('mouseenter', function() {
        this.querySelector('.tooltip-primary').style.visibility = 'visible';
        this.querySelector('.tooltip-primary').style.opacity = '1';

    });

    document.querySelector('.btn').addEventListener('mouseleave', function() {
        this.querySelector('.tooltip-primary').style.visibility = 'hidden';
        this.querySelector('.tooltip-primary').style.opacity = '0';
    });


    $(document).ready(function() {



        $("#statusSelect").select2();



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });




        $.ajax({
            type: "GET",
            url: "/get-user-name-dropdown",
            success: function(response) {
                if (response.data) {
                    var data = response.data;
                    var selectOptions = '<option  selected disabled> Select User </option>';

                    data.forEach(function(user) {
                        selectOptions += `<option class="" value="${user.id}">${user.full_name}</option>`;
                    });

                    $('#userSelect').html(selectOptions);
                }
            },
            error: function(error) {
                console.error('Error fetching data:', error);
            }
        });


        $("#userSelect").select2();



        function order_datatable() {

            $('#order_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-order-get",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'full_name',
                        name: 'full_name'
                    },
                    {
                        data: 'order_number',
                        name: 'order_number',
                        render: function(data, type, full, meta) {
                            return `<p class="text-primary fw-bold"> ${full.order_number} </p>`;

                        }

                    },
                    {
                        data: 'price',
                        name: 'price'
                    },
                    {
                        data: 'discount_amount',
                        name: 'discount_amount',

                    },
                    {
                        data: 'sub_total',
                        name: 'sub_total',

                    },
                    {
                        data: 'number_of_items',
                        name: 'number_of_items',

                    },
                    {

                        data: 'full_address',
                        name: 'full_address',
                        render: function(data, type, full, meta) {
                            return `<button type="button" class="btn btn_x btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="${full.full_address}">
                                    address
                                    </button>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, full, meta) {

                            if (data == '0') {
                                return '<span class="badge bg-warning">Pending</span>';
                            } else if (data == '1') {
                                return '<span class="badge bg-success">Completed</span>';
                            } else if (data == '2') {
                                return '<span class="badge bg-danger">Cancel</span>';
                            } else if (data == '3') {
                                return '<span class="badge bg-primary">Accepted</span>';
                            } else if (data == '4') {
                                return '<span class="badge bg-brown">Rejected</span>';
                            } else if (data == '5') {
                                return '<span class="badge bg-pink">Dispatched</span>';
                            } else if (data == '6') {
                                return '<span class="badge bg-purple">Delivered</span>';
                            }


                        }
                    },
                    {
                        data: 'order_date',
                        name: 'order_date',
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
                        data: 'status',
                        name: 'status',
                        render: function(data, type, full, meta) {

                            return `<a href="/order-view-page/${full.id}"  ><i class="fa fa-eye eye_icon_order"  id="${full.id}" data-bs-target="#order_modal" data-bs-toggle="modal" aria-hidden="true"></i></a>`;

                        }
                    }

                ]
            });




        }





        function getUrlParameter(name) {
                name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
                var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
                var results = regex.exec(location.search);
                return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
            };

        var Order_Status_Value = getUrlParameter('status_value');

        // alert(Order_Status_Value)

        if(!Order_Status_Value){
            order_datatable();
        }

        if(Order_Status_Value == "pending")
        {  
            var status  = 0;
            status_wise_data(status);
        }
        else if(Order_Status_Value == "completed"){
            var status = 1;
            status_wise_data(status);

        }
        else if(Order_Status_Value == "cancel")
        {
            var status = 2;
            status_wise_data(status);
        }
        else if(Order_Status_Value == "total")
        {
            var status = 2;
            order_datatable();
        }
       
       

        function status_wise_data(status){

            var full_name = null;
            
            $.ajax({
                type: "POST",
                url: "/filter-order-table",
                data: {
                    full_name: full_name,
                    status: status
                },
                dataType: "json",
                success: function(response) {
                    // $('#order_datatable').DataTable().clear().destroy();


                    $('#order_datatable').DataTable({
                        processing: true,
                        serverSide: false,
                        data: response.data,
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'full_name',
                                name: 'full_name'
                            },
                            {
                                data: 'order_number',
                                name: 'order_number',
                                render: function(data, type, full, meta) {
                                    return `<p class="text-primary fw-bold"> ${full.order_number} </p>`;

                                }

                            },
                            {
                                data: 'price',
                                name: 'price'
                            },
                            {
                                data: 'discount_amount',
                                name: 'discount_amount',

                            },
                            {
                                data: 'sub_total',
                                name: 'sub_total',

                            },
                            {
                                data: 'number_of_items',
                                name: 'number_of_items',

                            },
                            {

                                data: 'full_address',
                                name: 'full_address',
                                render: function(data, type, full, meta) {
                                    return `
                                    <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-primary" data-bs-offset="0,4" data-bs-placement="top" title="${full.full_address}">
                                    address
                                    </button>
                                    `;
                                }
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data, type, full, meta) {
                                    if (data == '0') {
                                        return '<span class="badge bg-warning">Pending</span>';
                                    } else if (data == '1') {
                                        return '<span class="badge bg-success">Completed</span>';
                                    } else if (data == '2') {
                                        return '<span class="badge bg-danger">Cancel</span>';
                                    } else if (data == '3') {
                                        return '<span class="badge bg-primary">Accepted</span>';
                                    } else if (data == '4') {
                                        return '<span class="badge bg-brown">Rejected</span>';
                                    } else if (data == '5') {
                                        return '<span class="badge bg-pink">Dispatched</span>';
                                    } else if (data == '6') {
                                        return '<span class="badge bg-purple">Delivered</span>';
                                    }

                                }
                            },
                            {
                                data: 'order_date',
                                name: 'order_date',
                            },
                            {
                                data: 'created_at',
                                name: 'created_at',
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data, type, full, meta) {

                                    return `<a href="/order-view-page/${full.id}"  ><i class="fa fa-eye eye_icon_order"  id="${full.id}" data-bs-target="#order_modal" data-bs-toggle="modal" aria-hidden="true"></i></a>`;

                                }
                            }

                        ]
                    });



                },
                error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });

        }



        $(document).on("click", "#filter_clear_btn", function() {


            $('#order_datatable').DataTable().clear().destroy();

            order_datatable();


        });


        $(document).on("click", "#filter_order_btn", function() {
            var full_name = $("#userSelect").val();
            var status = $("#statusSelect").val();

            $.ajax({
                type: "POST",
                url: "/filter-order-table",
                data: {
                    full_name: full_name,
                    status: status
                },
                dataType: "json",
                success: function(response) {
                    $('#order_datatable').DataTable().clear().destroy();


                    $('#order_datatable').DataTable({
                        processing: true,
                        serverSide: false,
                        data: response.data,
                        columns: [{
                                data: 'id',
                                name: 'id'
                            },
                            {
                                data: 'full_name',
                                name: 'full_name'
                            },
                            {
                                data: 'order_number',
                                name: 'order_number',
                                render: function(data, type, full, meta) {
                                    return `<p class="text-primary fw-bold"> ${full.order_number} </p>`;

                                }

                            },
                            {
                                data: 'price',
                                name: 'price'
                            },
                            {
                                data: 'discount_amount',
                                name: 'discount_amount',

                            },
                            {
                                data: 'sub_total',
                                name: 'sub_total',

                            },
                            {
                                data: 'number_of_items',
                                name: 'number_of_items',

                            },
                            {

                                data: 'full_address',
                                name: 'full_address',
                                render: function(data, type, full, meta) {
                                    return `
                                    <button type="button" class="btn btn-primary" data-bs-toggle="tooltip" data-bs-custom-class="tooltip-primary" data-bs-offset="0,4" data-bs-placement="top" title="${full.full_address}">
                                    address
                                    </button>
                                    `;
                                }
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data, type, full, meta) {
                                    if (data == '0') {
                                        return '<span class="badge bg-warning">Pending</span>';
                                    } else if (data == '1') {
                                        return '<span class="badge bg-success">Completed</span>';
                                    } else if (data == '2') {
                                        return '<span class="badge bg-danger">Cancel</span>';
                                    } else if (data == '3') {
                                        return '<span class="badge bg-primary">Accepted</span>';
                                    } else if (data == '4') {
                                        return '<span class="badge bg-brown">Rejected</span>';
                                    } else if (data == '5') {
                                        return '<span class="badge bg-pink">Dispatched</span>';
                                    } else if (data == '6') {
                                        return '<span class="badge bg-purple">Delivered</span>';
                                    }

                                }
                            },
                            {
                                data: 'order_date',
                                name: 'order_date',
                            },
                            {
                                data: 'created_at',
                                name: 'created_at',
                            },
                            {
                                data: 'status',
                                name: 'status',
                                render: function(data, type, full, meta) {

                                    return `<a href="/order-view-page/${full.id}"  ><i class="fa fa-eye eye_icon_order"  id="${full.id}" data-bs-target="#order_modal" data-bs-toggle="modal" aria-hidden="true"></i></a>`;

                                }
                            }

                        ]
                    });



                },
                error: function(error) {
                    console.error('Error fetching data:', error);
                }
            });


        });


    });
</script>