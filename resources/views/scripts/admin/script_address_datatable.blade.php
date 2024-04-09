<script type="text/javascript">
    $(document).ready(function() {



        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        function address_datatable() {

            $('#address_datatable').DataTable({
                processing: true,
                serverSide: false,
                ajax: "/admin-datatable-address-get",
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'user_id',
                        name: 'user_id'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'country',
                        name: 'country',

                    },
                    {
                        data: 'full_address',
                        name: 'full_address',

                    },
                    {
                        data: 'pincode',
                        name: 'pincode',

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

        address_datatable();



    });
</script>