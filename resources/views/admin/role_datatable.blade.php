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


@section('title', 'Role Datatable')
@section('content')



<nav aria-label="breadcrumb" id="breadcrumb_nav" >
 <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Roles</li>
 </ol>
</nav>



<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label text-center">
                    <h5 class="card-title mb-0">Role</h5>
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
                        <button class="btn btn-secondary create-new btn-primary" data-bs-toggle="modal" data-bs-target="#insert_role_form" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add role Record</span>
                            </span>
                        </button>

                        <div class="modal fade" id="insert_role_form" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel1">Add New Role</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="form_role_new_insert">
                                            <div class="row">
                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">Role Name:</label>
                                                    <input type="text" id="role_name_input" name="role_name_input" class="form-control" placeholder="Enter Role Name">
                                                </div>

                                            </div>

                                        </form>
                                    </div>
                                    <div class="modal-footer  ">
                                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="button" id="form_add_role_save_btn" class="btn btn-primary">Save</button>
                                    </div>
                                </div>

                            </div>
                        </div>



                    </div>
                </div>
            </div>
            <table class="datatables table table-bordered dataTable no-footer dtr-column" id="role_datatable" aria-describedby="DataTables_Table_0_info" style="width: 1202px;">
                <thead>
                    <tr>
                        <th class="sorting " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 110.2px;" aria-label="Name: activate to sort column ascending">ID</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 135.2px;" aria-label="Salary: activate to sort column ascending">ROLE</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">CREATED_AT</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">RIGHTS</th>


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









<div class="modal fade" id="rights_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel3">Admin Rights</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form id="table_rights_form" class="product_edit_form_admin">
                    <input type="hidden" name="role_rights_id" id="role_rights_id">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">Page Name</th>
                                <th scope="col">is_view</th>
                                <th scope="col">is_update</th>
                                <th scope="col">is_delete</th>
                                <th scope="col">is_created</th>

                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th scope="row">category</th>
                                <td><input class="form-check-input category_cb_rights" type="checkbox" value="" id="is_view_category" /></td>
                                <td><input class="form-check-input category_cb_rights" type="checkbox" value="" id="is_update_category" /></td>
                                <td><input class="form-check-input category_cb_rights" type="checkbox" value="" id="is_delete_category" /></td>
                                <td><input class="form-check-input category_cb_rights" type="checkbox" value="" id="is_created_category" /></td>
                            </tr>
                            <tr>
                                <th scope="row">companies</th>
                                <td><input class="form-check-input companies_cb_rights" type="checkbox" value="" id="is_view_companies" /></td>
                                <td><input class="form-check-input companies_cb_rights" type="checkbox" value="" id="is_update_companies" /></td>
                                <td><input class="form-check-input companies_cb_rights" type="checkbox" value="" id="is_delete_companies" /></td>
                                <td><input class="form-check-input companies_cb_rights" type="checkbox" value="" id="is_created_companies" /></td>
                            </tr>
                            <tr>
                                <th scope="row">products</th>
                                <td><input class="form-check-input products_cb_rights" type="checkbox" value="" id="is_view_products" /></td>
                                <td><input class="form-check-input products_cb_rights" type="checkbox" value="" id="is_update_products" /></td>
                                <td><input class="form-check-input products_cb_rights" type="checkbox" value="" id="is_delete_products" /></td>
                                <td><input class="form-check-input products_cb_rights" type="checkbox" value="" id="is_created_products" /></td>
                            </tr>
                            <tr>
                                <th scope="row">user </th>
                                <td><input class="form-check-input user_cb_rights" type="checkbox" value="" id="is_view_user" /></td>
                                <td><input class="form-check-input user_cb_rights" type="checkbox" value="" id="is_update_user" /></td>
                                <td><input class="form-check-input user_cb_rights" type="checkbox" value="" id="is_delete_user" /></td>
                                <td><input class="form-check-input user_cb_rights" type="checkbox" value="" id="is_created_user" /></td>
                            </tr>

                        </tbody>
                    </table>



                </form>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="role_save_btn" class="btn btn-primary"> Save </button>
            </div>
        </div>
    </div>
</div>






@section('scripts')




@include('scripts.admin.script_role_datatable')



@endsection


@stop