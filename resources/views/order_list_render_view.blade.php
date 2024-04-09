@if($orders != "")

@foreach($orders as $items)
<div class="w-fit mb-6 bg-white">
    <div id="item_container_{{ $items->id }}" style="width:280px;" class="  rounded-lg shadow-lg p-6">
        <a href="orders-page-view/{{$items->id }}" style="color:transparent;" class="w-full ">
            <div class="flex flex-col md:flex-row md:justify-between ">
                <div class="md:mr-6 md:w-full">
                    <p class="mt-1 text-base text-gray-900">Order Date: {{ substr_replace($items->order_date, "", -9) }}</p>
                    <p id="quantity_{{$items->id }}" class="mt-1 text-base text-gray-900">Total Items: {{ $items->number_of_items }}</p>
                    <p id="price_{{$items->id }}" class="mt-1 text-base text-gray-900">Total: {{ $items->price }}</p>
                    <p id="discount_{{$items->id }}" class="mt-1 text-base text-gray-900">Discount: {{ $items->discount_amount }}</p>
                    <p class="mt-1 text-base text-gray-900">Amount to Pay: {{$items->sub_total}}</p>

                    @if($items->status == 0)
                    <p id="main_discount_amount" class="text-gray-700 mt-2">Status: <span class="bg-amber-500 ml-1 text-white text-base font-medium px-2.5 py-0.5 rounded">Pending</span></p>
                    @endif

                    @if($items->status == 1)
                    <p id="main_discount_amount" class="text-gray-700 mt-2">Status: <span class="bg-green-500 ml-1 text-white text-base font-medium px-2.5 py-0.5 rounded">Completed</span></p>
                    @endif

                    @if($items->status == 2)
                    <p id="main_discount_amount" class="text-gray-700 mt-2">Status: <span class="bg-red-500 ml-1 text-white text-base font-medium px-2.5 py-0.5 rounded">Cancel</span></p>
                    @endif

                    @if($items->status == 3)
                    <p id="main_discount_amount" class="text-gray-700 mt-2">Status: <span class="bg-blue-500 ml-1 text-white text-base font-medium px-2.5 py-0.5 rounded">Accepted</span></p>
                    @endif

                    @if($items->status == 4)
                    <p id="main_discount_amount" class="text-gray-700 mt-2">Status: <span class="bg-amber-900 ml-1 text-white text-base font-medium px-2.5 py-0.5 rounded">Rejected</span></p>
                    @endif

                    @if($items->status == 5)
                    <p id="main_discount_amount" class="text-gray-700 mt-2">Status: <span class="bg-pink-500 ml-1 text-white text-base font-medium px-2.5 py-0.5 rounded">Dispatched</span></p>
                    @endif

                    @if($items->status == 6)
                    <p id="main_discount_amount" class="text-gray-700 mt-2">Status: <span class="bg-purple-500 ml-1 text-white text-base font-medium px-2.5 py-0.5 rounded">Delivered</span></p>
                    @endif


                </div>
                <div class="mt-4 md:mt-0 md:ml-6 md:flex md:space-x-6">
                    <!-- Any additional elements here -->
                </div>
            </div>
        </a>

      
       

    </div>
</div>
@endforeach

@endif