@extends('frontlayout')
@section('content')
<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600;700&display=swap');

	::selection{
	background: rgba(23,162,184,0.3);
	}
	.wrapper{
	max-width: 1200px;
	margin: auto;
	padding: 0 20px;
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: space-between;
	}
	.wrapper .box{
	background: #fff;
	width: calc(33% - 10px);
	padding: 25px;
	border-radius: 3px;
	box-shadow: 0px 4px 8px rgba(0,0,0,0.15);
	}
	.wrapper .box i.quote{
	font-size: 20px;
	color: #17a2b8;
	}
	.wrapper .box .content{
	display: flex;
	flex-wrap: wrap;
	align-items: center;
	justify-content: space-between;
	padding-top: 10px;
	}
	.box .info .name{
	font-weight: 600;
	font-size: 17px;
	}
	.box .info .job{
	font-size: 16px;
	font-weight: 500;
	color: #17a2b8;
	}
	.box .info .stars{
	margin-top: 2px;
	}
	.box .info .stars i{
	color: #17a2b8;
	}
	.box .content .image{
	height: 75px;
	width: 75px;
	padding: 3px;
	background: #17a2b8;
	border-radius: 50%;
	}
	.content .image img{
	height: 100%;
	width: 100%;
	object-fit: cover;
	border-radius: 50%;
	border: 2px solid #fff;
	}
	.box:hover .content .image img{
	border-color: #fff;
	}
	@media (max-width: 1045px) {
	.wrapper .box{
		width: calc(50% - 10px);
		margin: 10px 0;
	}
	}
	@media (max-width: 702px) {
	.wrapper .box{
		width: 100%;
	}
	}
	.footer {
	position: fixed;
	left: 0;
	bottom: 0;
	width: 100%;
	background-color: rgb(28, 18, 74);
	color: white;
	text-align: center;
	}
</style>
	<!-- Slider Section Start -->
	<div class="col-12">
		<div class="container-fluid">
			<div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
			<div class="carousel-inner">
				@foreach($banners as $index => $banner)
				<div class="carousel-item @if($index==0) active @endif">
					<img src="{{ asset('storage/app/' . $banner->banner_src) }}" class="d-block w-100" alt={{ $banner->alt_text }} width="1000" height="600">

				</div>
				@endforeach
			</div>
			<button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Previous</span>
			</button>
			<button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="visually-hidden">Next</span>
			</button>
			</div>
		</div>
	</div>
	<!-- Slider Section End -->

	<!-- Service Section Start -->
	<div class="container mt-5">
		<h1 class="text-center border-bottom" id="services">Warm Welcome</h1>
		<div class="row mt-4 border-bottom">
			<div class="col-7">
				<h3 style="color:blueviolet;">Dear User :</h3>
				<p>Welcome to our exquisite hotel: We are thrilled to have you as our valued guest. Your comfort and satisfaction are our top priorities, and we are dedicated to ensuring your stay is nothing short of exceptional.
				</p>

				<h3 style="color:blueviolet;">Hospitality at its Finest :</h3>
				<p>Step into a world of luxury and warmth at <span style="color:blue;">The Grand Ark Hotel</span>. It is our pleasure to extend a warm welcome to you. Our dedicated team is committed to providing you with an unparalleled experience, where every detail is carefully crafted to exceed your expectations.
				</p>

				<h3 style="color:blueviolet;">Home Away from Home :</h3>
				<p>Welcome to your home away from home! At <span style="color:blue;">The Grand Ark Hotel</span>, we consider you not just a guest but a part of our extended family. We've created an atmosphere that combines the comfort of home with the indulgence of luxury, ensuring a stay that is both relaxing and memorable.
				</p>

				<h3 style="color:blueviolet;">Exclusive Elegance :</h3>
				<p>Welcome to an oasis of exclusive elegance! At <span style="color:blue;">The Grand Ark Hotel</span>, we take pride in offering a sophisticated retreat for our esteemed guests. Prepare to be immersed in luxury, where every moment is designed to exceed your expectations and create lasting memories.
				</p>
			</div>
			<div class="col-5">
				<img src="{{asset('public/img/images/reception_girl.jpg') }}" alt="" width="500" height="450">
			</div>
		</div>
	</div>
	<div class="container mt-5 border-bottom">
		<h1 class="text-center border-bottom" id="services">Our Services</h1>
		<div class="row">
			<p>At <span style="color:blue;">The Grand Ark Hotel</span>, we take pride in providing unparalleled service to our guests. Your comfort and satisfaction are our top priorities. Our attentive staff is here to assist you with any needs or requests you may have. From the moment you arrive to the time you depart, we are dedicated to making your stay a seamless and delightful experience.</p>
		</div>
		
		@foreach($services as $service)
		<div class="row my-4">
			<div class="col-md-3">
				<a href="#"><img class="img-thumbnail" style="width:100%;" src="{{asset('storage/app/'.$service->photo)}}" /></a>
			</div>
			<div class="col-md-8">
				<h3>{{$service->title}}</h3>
				<p>{{$service->small_desc}}</p>
				<p>
					<a href="{{url('service/'.$service->id)}}" class="btn btn-primary">Read More</a>
				</p>
			</div>
		</div>
		@endforeach
	</div>
	<!-- Service Section End -->
	<!-- Gallery Section Start -->
	<div class="container mt-5 border-bottom">
		<h1 class="text-center border-bottom" id="gallery">What We Offer ?</h1>
		<div class="row">
			<p>Wake up to breathtaking views from the comfort of your room at <span style="color:blue;">The Grand Ark Hotel</span>. Our offered rooms provide not only a cozy retreat but also a visual feast. Whether it's a stunning cityscape or serene landscapes, our carefully chosen locations and thoughtfully positioned windows ensure a captivating experience.</p>
		</div>
		<div class="row my-4">
			@foreach($roomTypes as $rtype)
			<div class="col-md-3">
				<div class="card">
					<h5 class="card-header">{{$rtype->title}}</h5>
					<div class="card-body">
                        	<a href="#" >
								<img class="card-img-top" src="{{asset('/public/uploads/Roomtype/'.$rtype->img_src)}} " alt="Card image cap" style="height: 271px">
                        	</a>
							{{-- <button class="btn btn-primary">Book Now</button> --}}
							<a href="{{ ('page/booking') }}" class="btn btn-primary" style="margin-top: 11px;
							">Book Now</a>
                        </td>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
	<!-- Gallery Section End -->

	<!-- Slider Section Start -->
	<div class="container mt-5">
		<h1 class="text-center mt-5 border-bottom" id="gallery">Testimonials</h1>
		<div class="row">
			<p>Our guests have rated us highly. Here are some of our testimonials from our happy clients.</p>
		</div>
		<div class="wrapper mb-5">
		<div class="box">
		  <i class="fas fa-quote-left quote"></i>
		  <p>I frequently stay at hotels for business, and this one stands out. The room was immaculate, the staff was incredibly helpful, and the amenities were top-notch. It made my work trip feel like a relaxing getaway. I'll definitely be booking here again!</p>
		  <div class="content">
			<div class="info">
			  <div class="name">Jitendra Solanki</div>
			  <div class="job">CEO</div>
			  <div class="stars">
				<i class="fas fa-star"></i>
				<i class="fas fa-star"></i>
				<i class="far fa-star"></i>
				<i class="far fa-star"></i>
				<i class="far fa-star"></i>
			  </div>
			</div>
			<div class="image">
			  <img src="{{asset('public/img/images/user1.jpg') }}" alt="">
			</div>
		  </div>
		</div>
		<div class="box">
		  <i class="fas fa-quote-left quote"></i>
		  <p>Our family had an amazing time at this hotel. The pool and recreational areas kept our kids entertained, and the spacious rooms made us feel right at home. The staff went above and beyond to make our stay enjoyable. We can't wait to come back next year!</p>
		  <div class="content">
			<div class="info">
			  <div class="name">Steven Chris</div>
			  <div class="job">YouTuber | Blogger</div>
			  <div class="stars">
				<i class="fas fa-star"></i>
				<i class="fas fa-star"></i>
				<i class="fas fa-star"></i>
				<i class="far fa-star"></i>
				<i class="far fa-star"></i>
			  </div>
			</div>
			<div class="image">
			  <img src="{{asset('public/img/images/user2.jpg') }}" alt="">
			</div>
		  </div>
		</div>
		<div class="box">
		  <i class="fas fa-quote-left  quote"></i>
		  <p>This hotel exceeded our expectations for a romantic weekend. The room was beautifully decorated, and the attention to detail was remarkable. The hotel's restaurant served delicious meals, and the  intimate escape. We left with wonderful memories.</p>
		  <div class="content">
			<div class="info">
			  <div class="name">Kristina Bellis</div>
			  <div class="job">Freelancer | Advertiser</div>
			  <div class="stars">
				<i class="fas fa-star"></i>
				<i class="fas fa-star"></i>
				<i class="fas fa-star"></i>
				<i class="fas fa-star"></i>
				<i class="far fa-star"></i>
			  </div>
			</div>
			<div class="image">
			  <img src="{{asset('public/img/images/user3.jpg') }}" alt="">
			</div>
		  </div>
		</div>
	  </div>
	</div>
	  <!-- Slider Section End -->

	  <div class="footer mt-5" style="background-color:#9abdd7 !important">
		<p>Copyright © 2024 The Grand Ark Hotel. All rights reserved.</p>
	  </div>

<!-- LightBox css -->
<link rel="stylesheet" type="text/css" href="{{asset('public/vendor')}}/lightbox2-2.11.3/dist/css/lightbox.min.css" />
<!-- LightBox Js -->
<script type="text/javascript" src="{{asset('public/vendor')}}/lightbox2-2.11.3/dist/js/lightbox.min.js"></script>
<style type="text/css">
	.hide{
		display: none;
	}
</style>
@endsection
</body>
</html>