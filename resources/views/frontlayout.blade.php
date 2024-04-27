<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Home Page</title>
	<link href="{{asset('public/bs5/bootstrap.min.css')}}" rel="stylesheet" type="text/css" >
	<script type="text/javascript" src="{{asset('public/vendor/bootstrap/scss/bootstrap.scss')}}"></script>
	<script type="text/javascript" src="{{asset('public/vendor/jquery/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('public/bs5/bootstrap.bundle.min.js')}}"></script>
	<style>
		.nav-link:hover{
			background-color: #6520d5;
			color: black;
		}
		
		</style>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-dark bg-dark" style="background-color: #e3f2fd !important ">
	  <div class="container">
	    <a class="navbar-brand" href="{{url('/')}}"@style="color:#6520d5">The Grand Ark Hotel</a>
	    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
	      <span class="navbar-toggler-icon"></span>
	    </button>
	    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
	      <div class="navbar-nav ms-auto">
	        <a class="nav-link" aria-current="page" href="{{url('page/servicedetail')}}">Services</a>
	        <a class="nav-link" href="#gallery">Gallery</a>
	        <a class="nav-link" href="{{url('page/about-us')}}">About Us</a>
	        <a class="nav-link" href="{{url('page/contact-us')}}">Contact Us</a>
	        @if(Session::has('customerlogin'))
	        <a class="nav-link" href="{{url('customer/add-testimonial')}}">Add Testimonial</a>
	        <a class="nav-link" href="{{url('logout')}}">Logout</a>
	        <a class="nav-link btn btn-sm btn-danger" href="{{url('booking')}}">Booking</a>
			<a class="nav-link" href="{{url('page/room')}}">Rooms</a>
	        @else
	        <a class="nav-link" href="{{url('page/frontlogin')}}">Login</a>
	        <a class="nav-link" href="{{url('page/register')}}">Register</a>
	        <a class="nav-link btn btn-primary" href="{{url('page/booking')}}">Booking</a>
	        @endif
	      </div>
	    </div>
	  </div>
	</nav>
		<main>
			@yield('content')
		</main>
	</body>
</html>