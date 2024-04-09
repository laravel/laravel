@extends('admin.admin_layout')

@section('css')
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/typeahead-js/typeahead.css" />
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css" />
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css" />
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css" />
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css" />
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/flatpickr/flatpickr.css" />
<!-- Row Group CSS -->
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/datatables-rowgroup-bs5/rowgroup.bootstrap5.css" />
<!-- Form Validation -->
<link rel="stylesheet" href="/admin_panel/assets/vendor/libs/formvalidation/dist/css/formValidation.min.css" />

<script type="text/javascript" src="{{asset('js/select.css')}}"></script>


@section('title', 'Order Datatable')
@section('content')

<link rel="stylesheet" href="{{asset('js/select.css')}}">

<style>
    .select2-results__option:hover {
        background-color: #5A8DEE !important;
        color: white;
        /* Optional: Change text color on hover */
    }



    .dataTables_length {
        margin-left: 35px;
    }
</style>











<nav aria-label="breadcrumb" id="breadcrumb_nav">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">Order</li>
    </ol>
</nav>


<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label text-center">
                    <h5 class="card-title mb-0">Order</h5>
                </div>

            </div>
            <div class="row">

                <div class="col-md-9 mx-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="statusSelect" class="form-label">Select Status</label>
                            <select id="statusSelect" class="form-select form-select-sm">
                                <option selected disabled>Select Status</option>
                                <option value="0">Pending</option>
                                <option value="1">Completed</option>
                                <option value="2">Cancelled</option>
                                <option value="3">Accepted</option>
                                <option value="4">Rejected</option>
                                <option value="5">Dispatched</option>
                                <option value="6">Delivered</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="userSelect" class="form-label">Select User</label>
                            <select id="userSelect" class="form-select form-select-sm">
                                <option selected disabled>Select User</option>
                                <!-- Populate options dynamically if needed -->
                            </select>
                        </div>

                        <div class="col-md-2 " style="margin-top: 22px;">
                            <button type="button" id="filter_order_btn" class="btn btn-sm btn-primary">Filter</button>
                        </div>
                        <div class="col " style="margin-top: 22px; margin-left: -70px;">
                            <button type="button" id="filter_clear_btn" class="btn btn-sm btn-danger">Clear</button>
                        </div>
                    </div>
                </div>



            </div>
            <table class="datatables table table-bordered dataTable no-footer dtr-column" id="order_datatable" aria-describedby="DataTables_Table_0_info" style="width: 1202px;">
                <thead>
                    <tr>
                        <th class="sorting " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 110.2px;" aria-label="Name: activate to sort column ascending">ID</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 117.2px;" aria-label="Email: activate to sort column ascending">USER</th>
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






@section('scripts')




@include('scripts.admin.script_order_datatable')



@endsection


@stop