<nav class="bg-blue-500" id="the_navbar">
  <div class="container mx-auto px-4 py-2">
    <div class="flex justify-between items-center">



      <a href="/home" class="flex items-center app-brand-link">
        <span class="app-brand-logo demo w-6 h-6">
          <svg width="26px" height="26px" viewBox="0 0 26 26" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <title></title>
            <defs>
              <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="linearGradient-1">
                <stop stop-color="white" offset="0%"></stop>
                <stop stop-color="white" offset="100%"></stop>
              </linearGradient>
              <linearGradient x1="0%" y1="0%" x2="100%" y2="100%" id="linearGradient-2">
                <stop stop-color="#FDAC41" offset="0%"></stop>
                <stop stop-color="#E38100" offset="100%"></stop>
              </linearGradient>
            </defs>
            <g id="Pages" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
              <g id="Login---V2" transform="translate(-667.000000, -290.000000)">
                <g id="Login" transform="translate(519.000000, 244.000000)">
                  <g id="Logo" transform="translate(148.000000, 42.000000)">
                    <g id="icon" transform="translate(0.000000, 4.000000)">
                      <path d="M13.8863636,4.72727273 C18.9447899,4.72727273 23.0454545,8.82793741 23.0454545,13.8863636 C23.0454545,18.9447899 18.9447899,23.0454545 13.8863636,23.0454545 C8.82793741,23.0454545 4.72727273,18.9447899 4.72727273,13.8863636 C4.72727273,13.5423509 4.74623858,13.2027679 4.78318172,12.8686032 L8.54810407,12.8689442 C8.48567157,13.19852 8.45300462,13.5386269 8.45300462,13.8863636 C8.45300462,16.887125 10.8856023,19.3197227 13.8863636,19.3197227 C16.887125,19.3197227 19.3197227,16.887125 19.3197227,13.8863636 C19.3197227,10.8856023 16.887125,8.45300462 13.8863636,8.45300462 C13.5386269,8.45300462 13.19852,8.48567157 12.8689442,8.54810407 L12.8686032,4.78318172 C13.2027679,4.74623858 13.5423509,4.72727273 13.8863636,4.72727273 Z" id="Combined-Shape" fill="#4880EA"></path>
                      <path d="M13.5909091,1.77272727 C20.4442608,1.77272727 26,7.19618701 26,13.8863636 C26,20.5765403 20.4442608,26 13.5909091,26 C6.73755742,26 1.18181818,20.5765403 1.18181818,13.8863636 C1.18181818,13.540626 1.19665566,13.1982714 1.22574292,12.8598734 L6.30410592,12.859962 C6.25499466,13.1951893 6.22958398,13.5378796 6.22958398,13.8863636 C6.22958398,17.8551125 9.52536149,21.0724191 13.5909091,21.0724191 C17.6564567,21.0724191 20.9522342,17.8551125 20.9522342,13.8863636 C20.9522342,9.91761479 17.6564567,6.70030817 13.5909091,6.70030817 C13.2336969,6.70030817 12.8824272,6.72514561 12.5388136,6.77314791 L12.5392575,1.81561642 C12.8859498,1.78721495 13.2366963,1.77272727 13.5909091,1.77272727 Z" id="Combined-Shape2" fill="url(#linearGradient-1)"></path>
                      <rect id="Rectangle" fill="url(#linearGradient-2)" x="0" y="0" width="7.68181818" height="7.68181818"></rect>
                    </g>
                  </g>
                </g>
              </g>
            </g>
          </svg>
        </span>
        <span class="app-brand-text text-white title_navbar_brand_logo demo menu-text font-semibold ms-2">QuickBuy</span>
      </a>





      <button id="toggleNavbarBtn" class="text-white focus:outline-none lg:hidden">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
        </svg>
      </button>

      <div class="hidden lg:flex lg:items-center lg:w-auto" id="navbarNav">
        <ul class="flex flex-col lg:flex-row list-none lg:ml-auto items-center" id="navbar_ul_bag">
          <li class="nav-item">
            <a href="/home" class="px-3 py-2 text-white font-semibold hover:text-gray-200 lg:mx-2">Home</a>
          </li>
          <li class="nav-item">
            <a href="/products" class="px-3 py-2 text-white font-semibold hover:text-gray-200 lg:mx-2">Products</a>
          </li>
          <li class="nav-item">
            <a href="/categories" class="px-3 py-2 text-white font-semibold hover:text-gray-200 lg:mx-2">Categories</a>
          </li>


          @if(!session()->has('status_navbar'))
          <li class="nav-item" id="register_navbar">
            <a href="/register" id="register_link_navbar" class="px-3 py-2 text-white font-semibold lg:mx-2 transition-colors duration-300 hover:text-blue-500 hover:bg-white rounded-lg">Register</a>
          </li>
          <li class="nav-item">
            <a href="{{ route('showLoginForm') }}" id="login_navbar" class="px-3 py-2 text-white font-semibold hover:text-gray-200 lg:mx-2">Login</a>
          </li>
          @endif

          @if(session()->has('navbar_full_name'))

          @php

          $id = Auth::user()->id;
          @endphp

          <li class="nav-item">
            <a href="/user-profile-page/{{$id}}" class="px-3 py-2 text-white font-semibold hover:text-gray-200 lg:mx-2">My Profile</a>
          </li>

          <li class="nav-item">
            <a href="/logout" class="px-3 py-2 text-white font-semibold hover:text-gray-200 lg:mx-2">Logout</a>
          </li>
          @endif



        </ul>



        <form method="POST" class="lg:ml-auto lg:mr-0 flex flex-row items-center" action="/navbar-search-query" >
          @csrf 
          <input type="text" required id="navbar_search_box" class="bg-blue-800 text-white border-2 border-transparent focus:outline-none focus:border-gray-700 py-1 px-2 rounded-md mr-2" placeholder="Search">
          <button type="button" id="navbar_search_btn" class="bg-blue-800 hover:bg-blue-700 text-white font-semibold py-1 px-3 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700">Search</button>
        </form>


        @if(session()->get('cart_total_items')== 0)


        <a href="#" id="cart_Anchor" class="px-3 py-2  text-white font-semibold hover:text-gray-200 lg:mx-2">
          <div class="relative flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-gray-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            <span id="cart_items_count_navbar_icon" class="absolute -top-2 left-4 rounded-full bg-red-500 p-0.5 px-2 text-sm text-red-50">0</span>
          </div>
        </a>

        @elseif(session()->get('cart_total_items')!= 0)

        <a href="/cart-checkout" id="cart_Anchor" class="px-3 py-2  text-white font-semibold hover:text-gray-200 lg:mx-2">
          <div class="relative flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="white" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-8 w-8 text-gray-600">
              <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
            </svg>
            <span id="cart_items_count_navbar_icon" class="absolute -top-2 left-4 rounded-full bg-red-500 p-0.5 px-2 text-sm text-red-50"> {{session()->get('cart_total_items') }}</span>
          </div>
        </a>

        @endif



      </div>
    </div>
  </div>
</nav>





<script type="text/javascript" src="{{asset('js/jquery.js')}}"></script>

<script type="text/javascript" src="{{asset('js/ajax.js')}}"></script>
@include('scripts.navbar_script')