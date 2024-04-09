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


@section('title', 'Category Datatable')
@section('content')



<nav aria-label="breadcrumb" id="breadcrumb_nav" >
 <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Category</li>
 </ol>
</nav>





<div class="modal fade" id="category_Modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="category_add" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-mb-3 add_product_modal_input">
                            <label for="nameBasic" class="form-label">Category Name</label>
                            <input type="text" name="CategoryName" class="form-control" placeholder="Enter Category Name">
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="productImage" class="form-label required">Category Image</label>
                            <input type="file" class="form-control" id="image" name="categoryimage" accept="image/jpeg, image/png, image/jpg" required>
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="description" class="form-label required">Description</label>
                            <textarea class="form-control" placeholder="Enter Description" id="description" name="description" rows="5" cols="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="add_btn_category" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>




<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label text-center">
                    <h5 class="card-title mb-0">Category</h5>
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

                        <button class="btn btn-secondary create-new btn-primary" data-bs-toggle="modal" data-bs-target="#category_Modal" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add Category </span>
                            </span>
                        </button>
                        @endif

                        @if((Auth::user()->user_type != "super_admin" && $data->is_created == 1) )

                        <button class="btn btn-secondary create-new btn-primary" data-bs-toggle="modal" data-bs-target="#category_Modal" tabindex="0" aria-controls="DataTables_Table_0" type="button">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add Category </span>
                            </span>
                        </button>
                        @endif


                    </div>
                </div>
            </div>
            <table class="datatables table table-bordered dataTable no-footer dtr-column" id="category_datatable" aria-describedby="DataTables_Table_0_info" style="width: 1202px;">
                <thead>
                    <tr>
                        <th class="sorting " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 110.2px;" aria-label="Name: activate to sort column ascending">ID</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 117.2px;" aria-label="Email: activate to sort column ascending">NAME</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 135.2px;" aria-label="Salary: activate to sort column ascending">IMAGE</th>
                        <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">DESCRIPTION</th>
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





<div class="modal fade" id="edit_Category_Modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1"> Edit Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="category_edit" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-mb-3 add_product_modal_input">
                            <input type="hidden" name="category_id_rec_form" id="category_id_rec_form">
                            <label for="nameBasic" class="form-label">Category Name</label>
                            <input type="text" name="CategoryName_edit" id="CategoryName_edit" class="form-control" placeholder="Enter Category Name">
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="nameBasic" class="form-label">Category Current Image</label>
                            <br>
                            <img style="height: 150px;width: 150px;" id="product_img_edit" alt="" src="">
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="productImage" class="form-label required">Category Image</label>
                            <input type="file" class="form-control" id="categoryimage_edit" name="categoryimage_edit" accept="image/jpeg, image/png, image/jpg">
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="description" class="form-label required">Description</label>
                            <textarea class="form-control" placeholder="Enter Description" id="description_edit" name="description_edit" rows="5" cols="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" id="edit_btn_category" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>




@section('scripts')




@include('scripts.admin.script_category_datatable')



@endsection


@stop