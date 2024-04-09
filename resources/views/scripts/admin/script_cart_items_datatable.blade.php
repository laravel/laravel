<script type="text/javascript">
    $(document).ready(function() {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function cart_items_datatable() {

            $('#cart_items_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-cart-items-get",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'cart_id',
                        name: 'cart_id'
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

        cart_items_datatable();



    });
</script>