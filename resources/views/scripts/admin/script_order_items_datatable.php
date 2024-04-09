<script type="text/javascript">
    $(document).ready(function() {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function order_items_datatable() {

            $('#order_items_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-order-items-get",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'order_id',
                        name: 'order_id'
                    },

                    {
                        data: 'product_id',
                        name: 'product_id'
                    },

                    {
                        data: 'product_price',
                        name: 'product_price',

                    },
                    {
                        data: 'sub_total',
                        name: 'sub_total',

                    },
                    {
                        data: 'quantity',
                        name: 'quantity',
                    },
                    {
                        data: 'total_price',
                        name: 'total_price',
                    },

                    {
                        data: 'discount_amount',
                        name: 'discount_amount',
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

        order_items_datatable();



    });
</script>