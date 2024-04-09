@extends('admin.admin_layout')

@section('css')


@section('title', 'Order View')
@section('content')


<nav aria-label="breadcrumb" id="breadcrumb_nav">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="/admin-dashboard">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="/admin-order-datatable">Order</a></li>
        <li class="breadcrumb-item active" aria-current="page">View</li>
    </ol>
</nav>




<div class="container ">

    <div class="  d-flex   justify-content-end">
        <a href="/admin-order-datatable" class="btn btn-primary     ">Back</a>
    </div>

    <br>
    <br>

    <div class="row">

        <div class="col-md-6  p-3  ">
            <div class="bg-white " style="border-radius: 15px;">
                <div class="d-flex justify-content-center bg-white align-items-center mb-3">
                    <h3 class="mt-1 ml-3 p-2  ">User Details</h3>
                </div>
                <div class="container  bg-white mt-1">
                    <div class="row user-detail">
                        <div class="col-md-6">
                            <div class="mb-2"><i class="bx bx-user me-1"></i>: <span id="user_name">{{ $user_data->full_name }}</span></div>
                            <div class="mb-2"><i class='bx bx-phone'></i>: <span id="user_phone">{{ $user_data->phone }}</span></div>
                            <div class="mb-2"><i class='bx bx-envelope'></i>: <span id="user_email">{{ $user_data->email }}</span></div>

                        </div>
                        <div class="col-md-6">
                            <div class="mb-2"><i class='bx bxs-map'></i>: <span id="user_address">{{ $address_data->full_address }}</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class=" col-md-6  pt-2  bg-white" style="border-radius: 15px;">

            <div class="mx-auto ">
                <table class="table  ">
                    <tbody>
                        <tr>
                            <th class="text-center " style="font-size: 15px;" colspan="2">
                                <strong>Order Details</strong>
                            </th>
                        </tr>
                        <tr>
                            <th scope="row">Order Date:</th>
                            <td class="text-end fw-bold">{{ $order_data->order_date }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Order Number:</th>
                            <td class="text-end fw-bold">{{ $order_data->order_number }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Amount:</th>
                            <td class="text-end fw-bold">{{ $order_data->price }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Discount Amount:</th>
                            <td class="text-end fw-bold">{{ $order_data->discount_amount }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Subtotal:</th>
                            <td class="text-end fw-bold">{{ $order_data->sub_total }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Items:</th>
                            <td class="text-end fw-bold">{{ $order_data->number_of_items }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Status:</th>
                            <td class="text-end fw-bold">
                                @if($order_data->status == 0)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_status_modal" class="badge status_modal bg-warning">Pending</span>
                                @elseif($order_data->status == 1)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_status_modal" class="badge status_modal bg-success">Completed</span>

                                @elseif($order_data->status == 2)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_status_modal" class="badge status_modal bg-danger">Cancel</span>


                                @elseif($order_data->status == 3)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_status_modal" class="badge status_modal bg-primary">Accepted</span>
                                @elseif($order_data->status == 4)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_status_modal" class="badge status_modal bg-brown">Rejected</span>

                                @elseif($order_data->status == 5)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_status_modal" class="badge status_modal bg-pink">Dispatched</span>
                                @elseif($order_data->status == 6)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_status_modal" class="badge status_modal bg-purple">Delivered</span>
                                @endif
                            </td>
                        </tr>


                        <tr>
                            <th scope="row">Payment Status:</th>
                            <td class="text-end fw-bold">
                                @if($order_data->payment_status == 0)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_payment_status_modal" class="badge status_modal bg-warning">Pending</span>
                                @elseif($order_data->payment_status == 1)
                                <span style="cursor: pointer;" id="{{ $order_data->id }}" data-bs-toggle="modal" data-bs-target="#change_payment_status_modal" class="badge status_modal bg-success">Completed</span>
                                @endif
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

        </div>

    </div>


    <br><br>




    <div class="container">
        <div class="text-center">
            <h2>Order Items</h2>
            <hr>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
            @foreach($order_items_data as $data)
            <div class="col">

                <a href="/product-view-admin/{{ $data->id_product }}">
                    <div class="order-item bg-white shadow-lg rounded p-3">
                        <div class="d-flex flex-column flex-md-row">
                            <div class="order-item-image mb-3 mb-md-0">
                                <img src="/storage/product_images/{{ $data->image }}" alt="{{ $data->name }}" class="img-fluid" style="width: 200px; height: 200px;">
                            </div>
                            <div class="order-item-details ms-md-3">

                                <h4 class="text-sm font-bold text-dark">{{ $data->name }}</h4>
                                <p class=" text-dark mb-2"><strong>Price:</strong> {{ $data->price }}</p>
                                <p class=" text-dark mb-2"><strong>Quantity:</strong> {{ $data->quantity }}</p>
                                <p class="text-dark mb-2"><strong>Total:</strong> {{ $data->order_item_total_price }}</p>
                                <p class="text-dark mb-0"><strong>Discount:</strong> {{ $data->discount_amount }}</p>


                            </div>
                        </div>
                    </div>
                </a>

            </div>
            @endforeach
        </div>
    </div>


    <div class="modal fade" id="change_status_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Change Order Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_role_new_insert">
                        <div class="row">
                            <div class="form-group">
                                <label for="statusSelect">Status</label>
                                <select class="form-control" id="statusSelect">
                                    <option value="0">Pending</option>
                                    <option value="1">Completed</option>
                                    <option value="3">Accepted</option>
                                    <option value="4">Rejected</option>
                                    <option value="5">Dispatched</option>
                                    <option value="6">Delivered</option>

                                </select>
                            </div>

                            <div class="mb-3" id="textarea_status_reason_div" style="display: none;">
                                <label for="exampleFormControlTextarea1" class="form-label">Enter Reason</label>
                                <textarea class="form-control" id="textarea_status_reason" required rows="3"></textarea>
                            </div>


                        </div>

                    </form>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="change_status_save_btn" class="btn btn-primary">Save</button>
                </div>
            </div>

        </div>
    </div>


    <div class="modal fade" id="change_payment_status_modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel1">Change Payment Status</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form_role_new_insert">
                        <div class="row">
                            <div class="form-group">
                                <label for="payment_status_select">Status</label>
                                <select class="form-control" id="payment_status_select">
                                    <option value="0">Pending</option>
                                    <option value="1">Completed</option>
                                </select>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer ">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="payment_change_status_save_btn" class="btn btn-primary">Save</button>
                </div>
            </div>

        </div>
    </div>



</div>



@section('scripts')




@include('scripts.admin.script_order_view')



@endsection


@stop