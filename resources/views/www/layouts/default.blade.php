<!DOCTYPE html>
<html lang="en">

@include('www.layouts.partials.head')

<body class="@yield('body-class')">

	@include('www.layouts.partials.header')

	@yield('content')

	@include('www.layouts.partials.footer')

</body>

</html>