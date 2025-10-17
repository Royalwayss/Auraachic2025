@php $route_name = Route::currentRouteName();  @endphp
<script src="{{ asset('front/assets/js/vendor.js') }}"></script>
<script src="{{ asset('front/assets/js/main.js') }}?v=1.2"></script>
<script src="{{ asset('front/assets/js/custom.js') }}?v=1.2"></script>
@if($route_name == 'signin' || $route_name == 'signup' || $route_name == 'account' || $route_name == 'checkout')
<script src="{{ asset('front/assets/js/account.js') }}?v=1.2"></script>
@endif
@if($route_name == 'listing' || $route_name == 'product' || $route_name ==  'cart' || $route_name == 'checkout')
<script src="{{ asset('front/assets/js/product.js') }}?v=1.2"></script>
@endif
<script src="{{ asset('front/assets/js/all.min.js') }}"></script>
<script src="{{ asset('front/assets/js/sweetalert2.js') }}"></script> 
    