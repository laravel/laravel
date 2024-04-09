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


@section('title', 'Companies Datatable')
@section('content')





<nav aria-label="breadcrumb" id="breadcrumb_nav" >
 <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Companies</li>
 </ol>
</nav>



<div class="modal fade" id="company_modal_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Edit Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="company_edit" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-mb-3 add_product_modal_input">
                            <input type="hidden" name="company_id" id="company_id_edit">
                            <label for="companyName" class="form-label">Company Name</label>
                            <input type="text" id="companyName_edit" name="companyName_edit" class="form-control" placeholder="Enter Company Name">
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="productType" class="form-label required">Product Type:</label>
                            <select class="form-select" id="productType_edit" name="productType_edit">
                                <!-- Options for product type -->
                            </select>
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="address" class="form-label required">Address:</label>
                            <textarea class="form-control" id="address_edit" name="address_edit" cols="4" rows="5" placeholder="Enter Address"></textarea>
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="email" class="form-label required">Email</label>
                            <input type="email" class="form-control " id="email_edit" name="email_edit" placeholder="Enter Email" required>
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="phone" class="form-label required">Phone</label>
                            <input type="text" class="form-control " id="phone_edit" name="phone_edit" placeholder="Enter Phone" required>
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="country" class="form-label required">Country</label>
                            <input type="text" class="form-control " id="country_edit" name="country_edit" placeholder="Enter Country" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="edit_btn_company" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>



<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label text-center">
                    <h5 class="card-title mb-0">Companies</h5>
                </div>

            </div>
            <div class="row ">
                <div class="dt-buttons btn-group flex-wrap">
                    <div class="btn-group ">
                        <!-- <button class="btn btn-secondary buttons-collection dropdown-toggle btn-label-primary me-2" tabindex="0" aria-controls="DataTables_Table_0" type="button" aria-haspopup="dialog" aria-expanded="false">
                            <span>
                                <i class="bx bx-export me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Export</span>
                            </span>
                            <span class="dt-down-arrow"></span>
                        </button> -->
                        @if(Auth::user()->user_type == "super_admin")

                        <button class="btn btn-secondary create-new btn-primary" data-bs-toggle="modal" data-bs-target="#company_modal" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add company </span>
                            </span>
                        </button>
                        @endif

                        @if((Auth::user()->user_type != "super_admin" && $data->is_created == 1) )
                        <button class="btn btn-secondary create-new btn-primary" data-bs-toggle="modal" data-bs-target="#company_modal" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add company </span>
                            </span>
                        </button>
                        @endif

                    </div>
                </div>
            </div>
            <table class="datatables table table-bordered dataTable no-footer dtr-column" id="companies_datatable" aria-describedby="DataTables_Table_0_info" style="width: 1202px;">
                <thead>
                    <tr>
                        <th class="sorting " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 110.2px;" aria-label="Name: activate to sort column ascending">ID</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 117.2px;" aria-label="Email: activate to sort column ascending">NAME</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 135.2px;" aria-label="Salary: activate to sort column ascending">PRODUCT_TYPE</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">CATEGORY_ID</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">ADDERSS</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">EMAIL</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">PHONE</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">COUNTRY</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">CREATED_AT</th>

                        @if(Auth::user()->user_type == "super_admin")
                        <th class="no_arrow" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" data-orderable="false" aria-label="Status: activate to sort column ascending">
                            ACTIONS
                        </th>
                        @endif

                        @if((Auth::user()->user_type != "super_admin" && ( $data->is_delete == 1 || $data->is_update == 1) ))

                        <th class="no_arrow" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" data-orderable="false" aria-label="Status: activate to sort column ascending">
                            ACTIONS
                        </th>
                        @endif


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

<div class="modal fade" id="company_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Company</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="company_add" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-mb-3 add_product_modal_input">
                            <label for="companyName" class="form-label">Company Name</label>
                            <input type="text" id="companyName" name="companyName" class="form-control" placeholder="Enter Company Name">
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="productType" class="form-label required">Product Type:</label>
                            <select class="form-select" id="productType" name="productType">
                                <!-- Options for product type -->
                            </select>
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="address" class="form-label required">Address:</label>
                            <textarea class="form-control" id="address" name="address" cols="4" rows="5" placeholder="Enter Address"></textarea>
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="email" class="form-label required">Email</label>
                            <input type="email" class="form-control " id="email" name="email" placeholder="Enter Email" required>
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="phone" class="form-label required">Phone</label>
                            <input type="text" class="form-control " id="phone" name="phone" placeholder="Enter Phone" required>
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="country" class="form-label required">Country</label>
                            <input type="text" class="form-control " id="country" name="country" placeholder="Enter Country" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="add_btn_company" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>



@section('scripts')




@include('scripts.admin.script_companies_datatable')



@endsection


@stop