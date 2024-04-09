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
<link rel="stylesheet" href="{{asset('/js/admin_custom.css')}}">
<link rel="stylesheet" href="{{asset('/js/toast.css')}}">




@section('title', 'Products Datatable')
@section('content')



<nav aria-label="breadcrumb" id="breadcrumb_nav" >
 <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Products</li>
 </ol>
</nav>


<div class="card">
    <div class="card-datatable table-responsive pt-0">
        <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
            <div class="card-header flex-column flex-md-row">
                <div class="head-label text-center">
                    <h5 class="card-title mb-0">Products</h5>
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
                        <button class="btn btn-primary create-new btn-primary" id="add_product_modal_btn" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#Product_Modal" >
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add Products </span>
                            </span>
                        </button>
                        @endif

                        @if((Auth::user()->user_type != "super_admin" && $data->is_created == 1) )
                        <button class="btn btn-primary create-new btn-primary" id="add_product_modal_btn" tabindex="0" aria-controls="DataTables_Table_0" type="button" data-bs-toggle="modal" data-bs-target="#Product_Modal">
                            <span>
                                <i class="bx bx-plus me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add Products </span>
                            </span>
                        </button>
                        @endif
                    </div>





                    <div class="modal fade" id="Product_Modal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel1">Add Product</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form id="product_add" enctype="multipart/form-data">
                                        <div class="row">
                                            <div class="col-mb-3 add_product_modal_input">
                                                <label for="nameBasic" class="form-label">Product Name</label>
                                                <input type="text" name="productName" class="form-control" placeholder="Enter Product Name">
                                            </div>

                                            <div class="col mb-3 add_product_modal_input">
                                                <label for="productImage" class="form-label required"> Select Category:</label>
                                                <select class="form-select" id="category" name="category">
                                                </select>
                                            </div>

                                            <div class="col mb-3 add_product_modal_input">
                                                <label for="company" class="form-label required"> Select Company:</label>
                                                <select class="form-select " id="company" name="company">
                                                </select>
                                            </div>


                                            <div class="col-mb-3 add_product_modal_input">
                                                <label for="productImage" class="form-label required">Product Image</label>
                                                <input type="file" class="form-control " id="image" name="image[]" accept="image/jpeg, image/png, image/jpg" required>
                                            </div>
                                            <!-- Color -->
                                            <div class="col mb-3 add_product_modal_input">
                                                <label for="color" class="form-label required">Color</label>
                                                <input type="text" placeholder="Enter color" class="form-control " id="color" name="color">
                                            </div>
                                            <!-- Weight -->
                                            <div class="col-mb-3 add_product_modal_input">
                                                <label for="weight" class="form-label required">Weight</label>
                                                <input type="text" placeholder="Enter weight" class="form-control " id="weight" name="weight">
                                            </div>
                                            <!-- Quantity -->
                                            <div class="col mb-3 add_product_modal_input">
                                                <label for="quantity" class="form-label required">Quantity</label>
                                                <input type="number" placeholder="Enter Quantity" class="form-control " id="quantity" name="quantity" required>
                                            </div>

                                            <!-- Price -->
                                            <div class="col mb-3 add_product_modal_input">
                                                <label for="price" class="form-label required">Price</label>
                                                <input type="number" placeholder="Enter price" class="form-control " id="price" name="price" step="0.01" required>
                                            </div>
                                            <div class="col-mb-3 add_product_modal_input">
                                                <label for="discount" class="form-label required">Discount</label>
                                                <input type="text" placeholder="Enter discount (e.g., Rs10 or 10%)" class="form-control " id="discount" name="discount" required>
                                            </div>
                                            <!-- Description -->
                                            <div class="col-mb-3 add_product_modal_input">
                                                <label for="description" class="form-label required">Description</label>
                                                <br>
                                                <textarea class="form-control" placeholder="Enter Description" id="description" name="description" rows="3" cols="45"></textarea>
                                            </div>
                                    </form>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="button" id="add_btn_product" class="btn btn-primary">Save</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
    
    <table class="datatables table table-bordered dataTable no-footer dtr-column" id="products_datatable" aria-describedby="DataTables_Table_0_info" style="width: 1202px;">
        <thead>
            <tr>
                <th class="sorting " tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 110.2px;" aria-label="Name: activate to sort column ascending">ID</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 117.2px;" aria-label="Email: activate to sort column ascending">NAME</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 102.2px;" aria-label="Date: activate to sort column ascending">COMPANY_NAME</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 135.2px;" aria-label="Salary: activate to sort column ascending">CATEGORY_NAME</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">COLOR</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">WEIGHT</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">STOCK_QUANTITY</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">DESCRIPTION</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">PRICE</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">DISCOUNT_AMOUNT</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">DISCOUNT_PERCENT</th>

                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">CREATED_AT</th>
                <th class="sorting" tabindex="0" aria-controls="DataTables_Table_0" rowspan="1" colspan="1" style="width: 131.2px;" aria-label="Status: activate to sort column ascending">VIEW</th>


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


<div class="modal fade" id="product_edit_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel1">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <form id="product_edit_form" class="product_edit_form_admin" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-mb-3 add_product_modal_input">
                            <input type="hidden" name="product_id_edit" id="product_id_edit">

                            <label for="nameBasic" class="form-label">Product Name</label>
                            <input type="text" name="productName" id="productName_edit" class="form-control" placeholder="Enter Product Name">
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="productImage" class="form-label required"> Select Category:</label>
                            <select class="form-select" id="category_edit" name="category">
                            </select>
                        </div>

                        <div class="col mb-3 add_product_modal_input">
                            <label for="company" class="form-label required"> Select Company:</label>
                            <select class="form-select " id="company_edit" name="company">
                            </select>
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="image" class="form-label required"> Current Product image </label>
                            <br>
                            <img style="height: 80px;width: 80px;" id="product_img_edit" alt="">
                        </div>

                        <div class="col-mb-3 add_product_modal_input">
                            <label for="productImage" class="form-label required">change Image</label>
                            <input type="file" class="form-control " id="image" name="image[]" accept="image/jpeg, image/png, image/jpg">
                        </div>

                        <!-- Color -->
                        <div class="col mb-3 add_product_modal_input">
                            <label for="color" class="form-label required">Color</label>
                            <input type="text" placeholder="Enter color" class="form-control " id="color_edit" name="color">
                        </div>
                        <!-- Weight -->
                        <div class="col-mb-3 add_product_modal_input">
                            <label for="weight" class="form-label required">Weight</label>
                            <input type="text" placeholder="Enter weight" class="form-control " id="weight_edit" name="weight">
                        </div>
                        <!-- Quantity -->
                        <div class="col mb-3 add_product_modal_input">
                            <label for="quantity" class="form-label required">Quantity</label>
                            <input type="number" placeholder="Enter Quantity" class="form-control " id="quantity_edit" name="quantity" required>
                        </div>

                        <!-- Price -->
                        <div class="col mb-3 add_product_modal_input">
                            <label for="price" class="form-label required">Price</label>
                            <input type="number" placeholder="Enter price" class="form-control " id="price_edit" name="price" step="0.01" required>
                        </div>
                        <div class="col-mb-3 add_product_modal_input">
                            <label for="discount" class="form-label required">Discount</label>
                            <input type="text" placeholder="Enter discount (e.g., Rs10 or 10%)" class="form-control " id="discount_edit" name="discount" required>
                        </div>
                        <!-- Description -->
                        <div class="col-mb-3 add_product_modal_input">
                            <label for="description" class="form-label required">Description</label>
                            <br>
                            <textarea class="form-control" placeholder="Enter Description" id="description_edit" name="description" rows="3" cols="45"></textarea>
                        </div>
                </form>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" id="edit_btn_product" class="btn btn-primary">Save</button>
        </div>
    </div>
</div>


@section('scripts')




@include('scripts.admin.script_products_datatable')



@endsection


@stop