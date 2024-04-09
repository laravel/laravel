<script>
    $(document).ready(function() {


        function order_datatable() {

            var user_id = "{{  $user_data->id     }}"


            $('#order_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-order-view-get/" + user_id,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
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
                            return `<button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="${full.full_address}">
                                    address
                                    </button>`;
                        }
                    },
                    {
                        data: 'status',
                        name: 'status',
                        render: function(data, type, full, meta) {
                            if (data == '1') {
                                return '<span class="badge bg-success">Completed</span>';
                            }
                            if (data == '0') {
                                return '<span class="badge bg-warning">Pending</span>';
                            }
                            if (data == '2') {
                                return '<span class="badge bg-danger">Cancel</span>';
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

        order_datatable();



    });
</script>