@extends('admin.admin_layout')

@section('css')


@section('title', 'Dashboard')
@section('content')

<div class="row">



    <div class="col-6 col-md-3 col-lg-2 mb-4  ">


        <div class="card h-100">

            <a href="/admin-order-datatable?status_value=pending" style="text-decoration: none; " class="text-secondary">

                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-warning"><i class="bx bx-cart fs-4"></i></span>
                    </div>
                    <span class="d-block text-nowrap">Pending Orders</span>
                    <h2 id="pending_order_count" class="mb-0"></h2>
                </div>
            </a>

        </div>

    </div>

    <div class="col-6 col-md-3 col-lg-2 mb-4  ">
        <div class="card h-100">
            <a href="/admin-order-datatable?status_value=completed" style="text-decoration: none; " class="text-secondary">

                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-cart fs-4"></i></span>
                    </div>
                    <span class="d-block text-nowrap">Completed Orders</span>
                    <h2 id="completed_order_count" class="mb-0"></h2>
                </div>
            </a>
        </div>
    </div>

    <div class="col-6 col-md-3 col-lg-2 mb-4  ">

        <div class="card h-100">
            <a href="/admin-order-datatable?status_value=cancel" style="text-decoration: none; " class="text-secondary">

                <div class="card-body text-center">
                    <div class="avatar mx-auto mb-2">
                        <span class="avatar-initial rounded-circle bg-label-danger"><i class="bx bx-cart fs-4"></i></span>
                    </div>
                    <span class="d-block text-nowrap">Cancel Orders</span>
                    <h2 id="cancel_order_count" class="mb-0"></h2>
                </div>
            </a>
        </div>

    </div>


    <div class="col-6 col-md-3 col-lg-2 mb-4  ">

        <div class="card h-100">
        <a href="/admin-order-datatable?status_value=total" style="text-decoration: none; " class="text-secondary" >

            <div class="card-body text-center">
                
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-primary"><i class="bx bx-cart fs-4"></i></span>
                </div>
                <span class="d-block text-nowrap">Total Orders</span>
                <h2 id="total_order_count" class="mb-0"></h2>
            </div>
        </a>
        </div>


    </div>

    <div class="col-6 col-md-3 col-lg-2 mb-4  ">

        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-dark   justify-content-center   "> <i class="bx bx-user me-1"></i> </span>
                </div>
                <span class="d-block text-nowrap">Users</span>
                <h2 id="user_count" class="mb-0"></h2>
            </div>
        </div>

    </div>


    <div class="col-6 col-md-3 col-lg-2 mb-4  ">

        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-info"><i class="bx bx-cube"></i></span>
                </div>
                <span class="d-block text-nowrap">Products</span>
                <h2 id="products_count" class="mb-0"></h2>
            </div>
        </div>


    </div>


    <div class="col-6 col-md-3 col-lg-2 mb-4  ">

        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-danger">
                        <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>
                    </span>
                </div>
                <span class="d-block text-nowrap">Out of Stock </span>
                <h2 id="out_of_stock_count" class="mb-0"></h2>
            </div>
        </div>


    </div>


    <div class="col-6 col-md-3 col-lg-2 mb-4  ">

        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-info">
                        <i class="bx bx-trending-up"></i>
                    </span>
                </div>
                <span class="d-block text-nowrap">Today's Sales </span>
                <h2 id="today_sales_total_count" class="mb-0"></h2>
            </div>
        </div>


    </div>



    <div class="col-6 col-md-3 col-lg-2 mb-4  ">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="avatar mx-auto mb-2">
                    <span class="avatar-initial rounded-circle bg-label-warning">
                        <i class='bx bxs-building'></i>
                    </span>
                </div>
                <span class="d-block text-nowrap">Companies </span>
                <h2 id="companies_count" class="mb-0"></h2>
            </div>
        </div>
    </div>



    <br>











    <div class="card">
        <div class="card-header header-elements p-3 my-n1">
            <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2">Monthly Sales</h5>
            <div class="card-action-element ms-auto py-0">
                <!-- <div class="dropdown">
                    <button type="button" class="btn dropdown-toggle p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Yesterday</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7 Days</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30 Days</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current Month</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last Month</a></li>
                    </ul>
                </div> -->
            </div>
        </div>
        <div class="card-body">
            <canvas id="barChart" class="chartjs" data-height="400"></canvas>
        </div>
    </div>





    <br><br><br>

    <div class="card" style="margin-top: 20px;">
        <div class="card-header header-elements p-3 my-n1">
            <h5 class="card-title mb-0 pl-0 pl-sm-2 p-2">Monthly New User</h5>
            <div class="card-action-element ms-auto py-0">
                <!-- <div class="dropdown">
                    <button type="button" class="btn dropdown-toggle p-0" data-bs-toggle="dropdown" aria-expanded="false"><i class="bx bx-calendar"></i></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Today</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Yesterday</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 7 Days</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last 30 Days</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Current Month</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item d-flex align-items-center">Last Month</a></li>
                    </ul>
                </div> -->
            </div>
        </div>
        <div class="card-body">
            <canvas id="user_chart" class="chartjs" data-height="400"></canvas>
        </div>
    </div>





</div>




@section('scripts')




@include('scripts.admin.script_dashboard')



@endsection


@stop