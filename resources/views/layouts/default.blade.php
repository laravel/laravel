<!DOCTYPE html>
<html lang="en">

@include('layouts.partials.head')

<body class="@yield('body-class')">

	@include('layouts.partials.header')

	@yield('content')

	@include('layouts.partials.footer')

</body>

</html>