@extends('nav_layout')

@section('title', 'About Us')
@section('content')




<header class="py-5 ml-2">
    <div class="container mx-auto">
        <h1 class="text-5xl">About Us</h1>
    </div>
</header>

<div class="container mx-auto px-4">
    <section class="mb-12">
        <h2 class="text-3xl font-bold mb-6">Our Story</h2>
        <p class="text-base text-gray-700 leading-7">Welcome to Frest, your ultimate destination for all your e-commerce needs. We are passionate about providing our customers with a seamless shopping experience, offering a wide range of products, from electronics to fashion, at competitive prices.</p>
        <p class="text-base text-gray-700 leading-7">Our journey began with a simple vision: to create a platform where people can discover, explore, and shop for their favorite products conveniently and securely. With a commitment to quality, reliability, and customer satisfaction, we strive to exceed expectations with every purchase.</p>
    </section>

    <section class="mb-12">
        <h2 class="text-3xl font-bold mb-6">Our Mission</h2>
        <p class="text-base text-gray-700 leading-7">At Frest, our mission is to revolutionize the e-commerce industry by delivering innovative solutions that empower businesses and consumers alike. We aim to foster a community-driven marketplace where sellers can thrive and shoppers can find everything they need in one place.</p>
        <p class="text-base text-gray-700 leading-7">With a focus on sustainability, inclusivity, and social responsibility, we aspire to create a positive impact on the world while providing exceptional value and service to our customers.</p>
    </section>

    <section>
        <h2 class="text-3xl font-bold mb-6">Contact Us</h2>
        <p class="text-base text-gray-700 mb-4">Have questions or feedback? We'd love to hear from you!</p>
        <ul class="list-disc list-inside text-base text-gray-700 leading-7">
            <li>Email: info@frest.com</li>
            <li>Phone: +1 (555) 123-4567</li>
            <li>Address: 123 Green Street, Suite 200, New York, NY 10001</li>
        </ul>
    </section>
</div>






@section('scripts')

<!-- @include('scripts.add_address_page_script') -->


@endsection




@stop