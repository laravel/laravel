<script type="text/javascript">
    $(document).ready(function() {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function cart_datatable() {

            $('#cart_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-order-get",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                   
                    {
                        data: 'price',
                        name: 'price'
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
                        data: 'discount_amount',
                        name: 'discount_amount',
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
                    }

                ]
            });




        }

        cart_datatable();



    });
</script>