<!doctype html>
<html lang="en" class="no-js">
<head>
	@include('front.elements.meta-tag')
	@include('front.elements.style')
</head>
<body>
<div class="body-wrapper">
	@include('front.elements.loader')
	@include('front.elements.header')
	@yield('content')
	@include('front.elements.footer')
	@include('front.elements.modals')
	@include('front.elements.script')
</div>
</body>
</html>