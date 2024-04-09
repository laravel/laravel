<script type="text/javascript">
    $(document).ready(function() {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function posts_datatable() {

            $('#posts_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-posts-get",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },


                    {
                        data: 'product_id',
                        name: 'product_id'
                    },

                    {
                        data: 'image',
                        name: 'image',
                        render: function(data, type, full, meta) {
                            return '<img src="/storage/product_images/' + data + '" alt="' + data + '" width="100" height="100">';

                        }

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

        posts_datatable();



    });
</script>