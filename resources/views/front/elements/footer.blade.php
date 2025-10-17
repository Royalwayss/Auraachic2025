@php
use App\Models\Cart;
use App\Models\Category;
$cartitems = Cart::cartitems();
$categories = Category::getCategories($type='Front'); 
@endphp
 <!-- footer start -->
     <footer class="overflow-hidden">
         <div class="footer-top">
             <div class="container">
                 <div class="footer-widget-wrapper">
                     <div class="row justify-content-between">
                         <div class="col-xl-2 col-lg-2 col-md-6 col-12 footer-widget">
                             <div class="footer-widget-inner">
                                 <h4 class="footer-heading d-flex align-items-center justify-content-between">
                                     <span>Quick Links</span>
                                     <span class="d-md-none">
                                         <span class="d-md-none">
                                         <svg class="icon icon-dropdown" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline> </svg>
                                     </span>
                                     </span>
                                 </h4>
                                 <ul class="footer-menu list-unstyled mb-0 d-md-block">
                                     <li class="footer-menu-item"><a href="{{ route('aboutus') }}">About Us</a></li>
                                     <li class="footer-menu-item"><a href="javascript:;">Store Locator</a></li>
                                     <li class="footer-menu-item"><a href="javascript:;">Work with Us</a></li>
                                 </ul>
                             </div>
                         </div>
                         <div class="col-xl-2 col-lg-2 col-md-6 col-12 footer-widget">
                             <div class="footer-widget-inner">
                                 <h4 class="footer-heading d-flex align-items-center justify-content-between">
                                     <span>Products</span>
                                     <span class="d-md-none">
                                         <svg class="icon icon-dropdown" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline> </svg>
                                     </span>
                                 </h4>
                                 <ul class="footer-menu list-unstyled mb-0 d-md-block">
                                    @foreach($categories as $category)
									<li class="footer-menu-item"><a href="{{url($category['url'])}}">{{  $category['category_name'] }}</a></li>
                                    @endforeach 
                                 </ul>
                             </div>
                         </div>
                         <div class="col-xl-2 col-lg-2 col-md-6 col-12 footer-widget">
                             <div class="footer-widget-inner">
                                 <h4 class="footer-heading d-flex align-items-center justify-content-between">
                                     <span>Help</span>
                                     <span class="d-md-none">
                                         <svg class="icon icon-dropdown" xmlns="http://www.w3.org/2000/svg" width="24"  height="24" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1" stroke-linecap="round" stroke-linejoin="round"> <polyline points="6 9 12 15 18 9"></polyline> </svg>
                                     </span>
                                 </h4>
                                 <ul class="footer-menu list-unstyled mb-0 d-md-block">
                                     <li class="footer-menu-item"><a href="{{ url('privacy-policy') }}">Privacy Policy</a></li>
                                     <li class="footer-menu-item"><a href="javascript:;">Support</a></li>
                                     <li class="footer-menu-item"><a href="{{ route('contactus') }}">Contact Us</a></li>
                                 </ul>
                             </div>
                         </div>
                         <div class="col-xl-4 col-lg-5 col-md-6 col-12 footer-widget">
                             <div class="footer-widget-inner">
                                 <h4 class="footer-heading d-flex align-items-center justify-content-between"> <span>SUBSCRIBE</span> </h4>
                                 <div class="footer-newsletter">
                                     <p class="footer-text mb-3">Stay up to date with all the TRENDS.</p>
                                     <div class="newsletter-wrapper">
                                         <form action="javascript:;" data-action="{{ route('addsubscriber') }}" id="newsletter-form" class="footer-newsletter-form d-flex align-items-center">
                                             <input class="footer-newsletter-input bg-transparent" type="text"   placeholder="Your e-mail" autocomplete="off" id="newsletter-email">
                                             <button class="footer-newsletter-btn newsletter-btn-white newsletter-submit-btn" type="submit">SIGNUP</button>                                   
                                         </form>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
         <div class="footer-bottom">
             <div class="container">
                 <div class="footer-bottom-inner d-flex flex-wrap justify-content-md-between justify-content-center align-items-center">
                     <ul class="footer-bottom-menu list-unstyled d-flex flex-wrap align-items-center mb-0">
                         <li class="footer-menu-item"><a href="{{ url('privacy-policy') }}">Privacy Policy</a></li>
                         <li class="footer-menu-item"><a href="{{ url('term-and-conditions') }}">Terms & Conditions</a></li>
                         <li class="footer-menu-item"><a href="{{ url('shipping-policy') }}">Shipping Policy</a></li>
                         <li class="footer-menu-item"><a href="{{ url('cancellation-and-refunds	') }}">Cancellation and Refunds	</a></li>
                     </ul>
                     <p class="copyright footer-text">©<span class="current-year">{{ date('Y') }}</span> Aurrachic All Right Reserved.</p>
                 </div>
             </div>
         </div>
     </footer>
     <!-- footer end -->
     <!-- scrollup start -->
     <button id="scrollup">
         <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#fff"stroke-width="2" stroke-linecap="round" stroke-linejoin="round"> <polyline points="18 15 12 9 6 15"></polyline> </svg>
     </button>
     <!-- scrollup end -->
	 
	 <!-- whatsapp button start -->
	 <div class="whatsapp-icon">
		  <a href="https://wa.me/+919317733723" rel="nofollow" target="_blank">
			<img src="{{ asset('front/assets/img/icon/whatsapp.png') }}" alt="WhatsApp" title="WhatsApp" class="img-fluid">
		  </a>
	 </div>
	 <!-- whatsapp end start -->
	 
	 
	 
	 
	 
	 
	 
     <!-- drawer cart start -->
     <div class="offcanvas offcanvas-end" tabindex="-1" id="drawer-cart">
	 @include('front.pages.products.cart.include.cart-popup')
	 </div>
	 <!-- drawer cart end -->
	<div class="RouteDiv">
	    <div id="signin_route">{{ route('signin') }}</div>
	    <div id="productquickview_route">{{ route('productquickview') }}</div>
		<div id="addtocart_route">{{ route('addtocart') }}</div>
		<div id="updatecartitem_route">{{ route('updatecartitem') }}</div>
		<div id="deletecartitem_route">{{ route('deletecartitem') }}</div>
		<div id="addtowishlist_route">{{ route('addtowishlist') }}</div>
		<div id="orderaddressform_route">{{ route('orderaddressform') }}</div>
		<div id="saveorderaddress_route">{{ route('saveorderaddress') }}</div>
		<div id="setdefaultaddress_route">{{ route('setdefaultaddress') }}</div>
		<div id="deleteaddress_route">{{ route('deleteaddress') }}</div>
		<div id="getstatecity_route">{{ route('getstatecity') }}</div>
		<div id="getproductprice_route">{{ route('getproductprice') }}</div>
		<div id="checkout_route">{{ route('checkout') }}</div>
        <div id="checkpincode_route">{{ route('checkpincode') }}</div>
		<div id="removewishlist_route">{{ route('removewishlist') }}</div>
	</div>

     
