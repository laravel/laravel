@extends('admin.admin_layout')

@section('css')


@section('title', 'User View')
@section('content')



<style>
    .dataTables_length {
        margin-left: 20px;
    }
</style>



<nav aria-label="breadcrumb" id="breadcrumb_nav" >
 <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="/admin-user-datatable">User</a></li>
    <li class="breadcrumb-item active" aria-current="page">View</li>
 </ol>
</nav>



<div class="container ">

    <div class="  d-flex   justify-content-end">
        <a href="/admin-user-datatable" class="btn btn-primary ">Back</a>
    </div>

    <br>
    <br>

    <div class="row">




        <div class="col-md-12 p-3">
            <div class="bg-white rounded p-3" style="border-radius: 15px;">
                <div class="d-flex justify-content-center bg-white text-center mb-3">
                    <h3>User Details</h3>
                </div>
                <div class="container bg-white mt-1">
                    <div class="row user-detail">
                        <div class="col-12">
                            <div class="d-flex flex-column flex-md-row justify-content-between mb-2">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class="bx bx-user me-2"></i>
                                    <strong>:</strong> <span id="user_name" class="fs-5 ms-2">{{ $user_data->full_name }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class='bx bx-phone me-2'></i>
                                    <strong>:</strong> <span id="user_phone" class="fs-5 ms-2">{{ $user_data->phone }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <i class='bx bx-envelope me-2'></i>
                                    <strong>:</strong> <span id="user_email" class="fs-5 ms-2">{{ $user_data->email }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>




    </div>


    <br><br>






    <div class="text-center">
        <h2>Orders</h2>
        <hr>
    </div>


    @if( ( $order_data == "[]" ))

    <div class="fs-3 text-center">User hasn't placed any orders...</div>


    @endif

    @if($order_data != "[]" )


    <div class="card">
        <div class="card-datatable table-responsive pt-0">
            <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                <div class="card-header flex-column flex-md-row">
                    <div class="head-label text-center">
                        <h5 class="card-title mb-0"></h5>
                    </div>

                </div>
                <div class="row">




                </div>
                <table class="datatables table table-bordered dataTable no-footer dtr-column" id="order_datatable" aria-describedby="DataTables_Table_0_info" style="width: 1202px;">
                    <thead>
                        <tr>
                            <th class="sorting " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 110.2px;" aria-label="Name: activate to sort column ascending">ID</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 117.2px;" aria-label="Email: activate to sort column ascending">USER_ID</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 102.2px;" aria-label="Date: activate to sort column ascending">ORDER_NUMBER</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 135.2px;" aria-label="Salary: activate to sort column ascending">PRICE</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">DISCOUNT_AMOUNT</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">SUB_TOTAL</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">NUMBER_OF_ITEMS</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">ADDRESS</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">STATUS</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">ORDER_DATE</th>
                            <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">CREATED_AT</th>
                            <th class="" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" data-orderable="false" aria-label="Status: activate to sort column ascending">ACTION</th>

                        </tr>
                    </thead>
                    <tbody>
                        <tr class="odd">
                            <td valign="top" colspan="7" class="dataTables_empty">Loading...</td>
                        </tr>
                    </tbody>
                </table>
                <div class="row">
                    <div class="col-sm-12 col-md-6">
                    </div>
                    <div class="col-sm-12 col-md-6">



                    </div>
                </div>
            </div>
        </div>
    </div>




    @endif









</div>



@section('scripts')


@include('scripts.admin.script_user_view')





@endsection


@stop