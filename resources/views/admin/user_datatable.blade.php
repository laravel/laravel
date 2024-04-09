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


@section('title', 'User Datatable')
@section('content')


<nav aria-label="breadcrumb" id="breadcrumb_nav" >
 <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">User</li>
 </ol>
</nav>



<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label text-center">
                    <h5 class="card-title mb-0">User</h5>
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
                        <button class="btn btn-secondary create-new btn-primary" data-bs-toggle="modal" id="open_modal_user" data-bs-target="#user_modal_admin" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add User Record</span>
                            </span>
                        </button>
                        @endif

                        @if((Auth::user()->user_type != "super_admin" && $data->is_created == 1) )
                        <button class="btn btn-secondary create-new btn-primary" data-bs-toggle="modal" id="open_modal_user" data-bs-target="#user_modal_admin" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add User Record</span>
                            </span>
                        </button>
                        @endif



                        <div class="modal fade" id="user_modal_admin" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel1">Add User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="user_insert_form" enctype="multipart/form-data">
                                            <div class="row">
                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">First name</label>
                                                    <input type="text" name="first_name" id="first_name" class="form-control" placeholder="Enter First Name">
                                                </div>
                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">Last name</label>
                                                    <input type="text" name="last_name" id="last_name" class="form-control" placeholder="Enter Last Name">
                                                </div>

                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="productImage" class="form-label required"> Select Role:</label>
                                                    <select class="form-select" id="user_type_select" name="user_type_select">
                                                    </select>
                                                </div>


                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">email</label>
                                                    <input type="text" id="email" name="email" class="form-control" placeholder="Enter Email">
                                                </div>

                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">Phone</label>
                                                    <input type="text" maxlength="10" id="phone" name="phone" class="form-control" placeholder="Enter Phone ">
                                                </div>

                                                <div class="col mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">Password</label>
                                                    <input type="password" id="password" name="password" class="form-control" placeholder="Enter  Password">
                                                </div>


                                                <div class="col mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label"> Confirm Password</label>
                                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Enter Confirm Password">
                                                </div>


                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">City</label>
                                                    <input type="text" name="city" id="city" class="form-control" placeholder="Enter City">
                                                </div>

                                                <div class="col mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">Pincode</label>
                                                    <input type="text" maxlength="6" name="pincode" id="pincode" class="form-control" placeholder="Enter pincode">
                                                </div>


                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">State</label>
                                                    <input type="text" name="state" id="state" class="form-control" placeholder="Enter state">
                                                </div>

                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">Country</label>
                                                    <input type="text" id="country" name="country" class="form-control" placeholder="Enter country">
                                                </div>

                                                <div class="col-mb-3 add_product_modal_input">
                                                    <label for="nameBasic" class="form-label">Address</label>
                                                    <textarea class="form-control" placeholder="Enter Address" id="address" name="address" rows="3" cols="45"></textarea>

                                                </div>

                                        </form>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="button" id="add_btn_user" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
            </div>


        </div>
        <table class="datatables table table-bordered dataTable no-footer dtr-column" id="user_datatable" aria-describedby="DataTables_Table_0_info" style="width: 1202px;">
            <thead>
                <tr>
                    <th class="sorting " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 110.2px;" aria-label="Name: activate to sort column ascending">ID</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 117.2px;" aria-label="Email: activate to sort column ascending">FIRST_NAME</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 102.2px;" aria-label="Date: activate to sort column ascending">LAST_NAME</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 135.2px;" aria-label="Salary: activate to sort column ascending">FULL_NAME</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">EMAIL</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">ROLE_ID</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">USER_TYPE</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">PHONE</th>
                    <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">CREATED_AT</th>
                    <th class=" " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" data-orderable="false" aria-label="Status: activate to sort column ascending">view</th>


                    @if(Auth::user()->user_type === "super_admin")
                    <th class="no_arrow"  tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" data-orderable="false" aria-label="Status: activate to sort column ascending">
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






<div class="modal fade" id="user_modal_admin_edit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="user_edit_form" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-mb-3 add_product_modal_input">
                            <input type="hidden" name="user_edit_id_datatable" id="user_edit_id_datatable">
                            <label for="nameBasic" class="form-label">First name</label>
                            <input type="text" name="first_name_edit" id="first_name_edit" class="form-control" placeholder="Enter First Name">
                        </div>
                        <div class="col-mb-3 add_product_modal_input">
                            <label for="nameBasic" class="form-label">Last name</label>
                            <input type="text" name="last_name_edit" id="last_name_edit" class="form-control" placeholder="Enter Last Name">
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="productImage" class="form-label required"> Select Role:</label>
                            <select class="form-select" id="user_type_select_edit" name="user_type_select_edit">
                            </select>
                        </div>


                        <div class="col-mb-3 add_product_modal_input">
                            <label for="nameBasic" class="form-label">email</label>
                            <input type="text" id="email_edit" name="email_edit" class="form-control" placeholder="Enter Email">
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="nameBasic" class="form-label">Phone</label>
                            <input type="text" maxlength="10" id="phone_edit" name="phone_edit" class="form-control" placeholder="Enter Phone ">
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="nameBasic" class="form-label">Password</label>
                            <input type="password" id="password_edit" name="password_edit" class="form-control" placeholder="Enter  Password">
                        </div>


                        <div class="col mb-3 add_product_modal_input">
                            <label for="nameBasic" class="form-label"> Confirm Password</label>
                            <input type="password" name="password_confirmation_edit" id="password_confirmation_edit" class="form-control" placeholder="Enter Confirm Password">
                        </div>


                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="edit_btn_use_formr" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>




@section('scripts')




@include('scripts.admin.script_user_datatable')



@endsection


@stop