@php
use App\Models\Cart;
use App\Models\Category;
use App\Models\Banner;
$featured_banner = Banner::where('type','Featured')->where('status',1)->first(); 
$categories = Category::getCategories($type='Front'); 
$cartitems = Cart::cartitems();
$totalCartItems = $cartitems['totalCartItems'];
$RouteName = Route::currentRouteName(); 
@endphp
<div class="position-fixed notify col-sm-4" style="display: none;"></div>
<!-- announcement bar start -->
<div class="announcement-bar bg-4 py-1 py-lg-2">
   <div class="container">
      <div class="row align-items-center justify-content-between">
         <div class="col-lg-3 d-lg-block d-none">
            <div class="announcement-call-wrapper">
               <div class="announcement-call">
                  <a class="announcement-text text-white" href="tel:+918332983000">Call: +91
                  8332983000</a>
               </div>
            </div>
         </div>
         <div class="col-lg-6 col-12">
            <div class="announcement-text-wrapper d-flex align-items-center justify-content-center">
               <p class="announcement-text text-white">Flat - 30% off on all products</p>
            </div>
         </div>
         <div class="col-lg-3 d-lg-block d-none">
            <div class="announcement-meta-wrapper d-flex align-items-center justify-content-end">
               <div class="announcement-meta d-flex align-items-center">
                  <a class="announcement-login announcement-text text-white" @if(Auth::check()) href="{{ route('account',['profile']) }}"@else  href="{{ route('signin') }}" @endif>
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-user" width="10" height="11" viewBox="0 0 10 11" fill="none"><path d="M5 0C3.07227 0 1.5 1.57227 1.5 3.5C1.5 4.70508 2.11523 5.77539 3.04688 6.40625C1.26367 7.17188 0 8.94141 0 11H1C1 8.78516 2.78516 7 5 7C7.21484 7 9 8.78516 9 11H10C10 8.94141 8.73633 7.17188 6.95312 6.40625C7.88477 5.77539 8.5 4.70508 8.5 3.5C8.5 1.57227 6.92773 0 5 0ZM5 1C6.38672 1 7.5 2.11328 7.5 3.5C7.5 4.88672 6.38672 6 5 6C3.61328 6 2.5 4.88672 2.5 3.5C2.5 2.11328 3.61328 1 5 1Z" fill="#fff"/></svg>
                  <span>@if(Auth::check()) Account @else Login @endif</span>
                  </a>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<!-- announcement bar end -->
<!-- header start -->
<header class="sticky-header border-btm-black header-1">
   <div class="header-bottom">
      <div class="container">
         <div class="row align-items-center">
            <div class="col-lg-3 col-md-4 col-4">
               <div class="header-logo">
                  <a href="{{ route('home') }}" class="logo-main">
                  <img src="{{ asset('front/assets/img/logo.png') }}" loading="lazy" alt="auraachic logo" title="auraachic logo">
                  </a>
               </div>
            </div>
            <div class="col-lg-6 d-lg-block d-none">
               <nav class="site-navigation">
                  <ul class="main-menu list-unstyled justify-content-center">
                     <li class="menu-list-item nav-item @if($RouteName == 'home') active @endif">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                     </li>
                     <li class="menu-list-item nav-item has-megamenu">
                        <div class="mega-menu-header">
                           <a class="nav-link @if($RouteName == 'listing' || $RouteName == 'product') active @endif" href="#" > Shop </a>
                           <span class="open-submenu">
                             <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-dropdown" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                           </span>
                        </div>
                        <div class="submenu-transform submenu-transform-desktop">
                           <div class="container">
                              <ul class="submenu megamenu-container list-unstyled">
                                 @foreach($categories as $category)
                                 <li class="menu-list-item nav-item-sub">
                                    <div class="mega-menu-header">
                                       <a class="nav-link-sub nav-text-sub megamenu-heading" href="{{url($category['url'])}}">{{  $category['category_name'] }}</a>
                                    </div>
                                    @if(count($category['subcategories'])>0)
                                    <div class="submenu-transform megamenu-transform">
                                       <ul class="megamenu list-unstyled">
                                          @foreach($category['subcategories'] as $subcategory)
                                          <li class="menu-list-item nav-item-sub">
                                             <a class="nav-link-sub nav-text-sub" href="{{url($subcategory['url'])}}">{{  $subcategory['category_name'] }}</a>
                                          </li>
                                          @endforeach 
                                       </ul>
                                    </div>
                                    @endif
                                 </li>
                                 @endforeach
                                  @if(!empty($featured_banner) && !empty($featured_banner['image']))
								  <li class="menu-list-item nav-item-sub">
										<div class="mega-menu-header d-flex align-items-center justify-content-between">
										   <a class="mega-menu-img nav-link-sub nav-text-sub" href="{{ $featured_banner['link'] }}">
											   <img class="menu-img" src="{{ asset('front/images/banners/'.$featured_banner['image']) }}"   alt="{{ $featured_banner['alt'] }}" title="{{ $featured_banner['title'] }}">
											   <h2 class="img-menu-heading text_16 mt-2">Featured Collection</h2>												   
											   <div class="img-menu-action text_12 bg-transparent p-0">
												   <span>DISCOVER NOW</span>
												   <span><svg xmlns="http://www.w3.org/2000/svg" width="30" height="18" fill="#000" class="icon-right-long" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/></svg></span>
											   </div>
										   </a>
									   </div>
                                 </li>
                                 @endif								 
                              </ul>
                           </div>
                        </div>
                     </li>
                     <li class="menu-list-item nav-item"><a class="nav-link @if($RouteName == 'newarrival') active @endif" href="{{ route('newarrival') }}">New Arrivals</a></li>
                     <li class="menu-list-item nav-item"><a class="nav-link" href="javascript:;">Collection</a></li>
                     <li class="menu-list-item nav-item"><a class="nav-link" href="{{ route('contactus') }}">Contact</a></li>
                  </ul>
               </nav>
            </div>
            <div class="col-lg-3 col-md-8 col-8">
               <div class="header-action d-flex align-items-center justify-content-end">
                  <a class="header-action-item header-search" href="javascript:;">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-search" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M7.75 0.250183C11.8838 0.250183 15.25 3.61639 15.25 7.75018C15.25 9.54608 14.6201 11.1926 13.5625 12.4846L19.5391 18.4611L18.4609 19.5392L12.4844 13.5627C11.1924 14.6203 9.5459 15.2502 7.75 15.2502C3.61621 15.2502 0.25 11.884 0.25 7.75018C0.25 3.61639 3.61621 0.250183 7.75 0.250183ZM7.75 1.75018C4.42773 1.75018 1.75 4.42792 1.75 7.75018C1.75 11.0724 4.42773 13.7502 7.75 13.7502C11.0723 13.7502 13.75 11.0724 13.75 7.75018C13.75 4.42792 11.0723 1.75018 7.75 1.75018Z" fill="black"/></svg>
                  </a>
                  <a class="header-action-item header-wishlist ms-4  d-lg-block"  @if(Auth::check()) href="{{ route('account',['wishlist']) }}" @else href="{{ route('signin') }}" @endif>
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-wishlist" width="26" height="22" viewBox="0 0 26 22" fill="none"><path d="M6.96429 0.000183105C3.12305 0.000183105 0 3.10686 0 6.84843C0 8.15388 0.602121 9.28455 1.16071 10.1014C1.71931 10.9181 2.29241 11.4425 2.29241 11.4425L12.3326 21.3439L13 22.0002L13.6674 21.3439L23.7076 11.4425C23.7076 11.4425 26 9.45576 26 6.84843C26 3.10686 22.877 0.000183105 19.0357 0.000183105C15.8474 0.000183105 13.7944 1.88702 13 2.68241C12.2056 1.88702 10.1526 0.000183105 6.96429 0.000183105ZM6.96429 1.82638C9.73912 1.82638 12.3036 4.48008 12.3036 4.48008L13 5.25051L13.6964 4.48008C13.6964 4.48008 16.2609 1.82638 19.0357 1.82638C21.8613 1.82638 24.1429 4.10557 24.1429 6.84843C24.1429 8.25732 22.4018 10.1584 22.4018 10.1584L13 19.4036L3.59821 10.1584C3.59821 10.1584 3.14844 9.73397 2.69866 9.07411C2.24888 8.41426 1.85714 7.55466 1.85714 6.84843C1.85714 4.10557 4.13867 1.82638 6.96429 1.82638Z" fill="black"/></svg>
                  <span class="pro-count red totalWishlistItems">{{ totalWishlistItems() }}</span>
                  </a>
                  <a class="header-action-item header-cart ms-4" href="#drawer-cart"
                     data-bs-toggle="offcanvas">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-cart" width="24" height="26" viewBox="0 0 24 26" fill="none"><path d="M12 0.000183105C9.25391 0.000183105 7 2.25409 7 5.00018V6.00018H2.0625L2 6.93768L1 24.9377L0.9375 26.0002H23.0625L23 24.9377L22 6.93768L21.9375 6.00018H17V5.00018C17 2.25409 14.7461 0.000183105 12 0.000183105ZM12 2.00018C13.6562 2.00018 15 3.34393 15 5.00018V6.00018H9V5.00018C9 3.34393 10.3438 2.00018 12 2.00018ZM3.9375 8.00018H7V11.0002H9V8.00018H15V11.0002H17V8.00018H20.0625L20.9375 24.0002H3.0625L3.9375 8.00018Z" fill="black"/></svg>
                     <span class="pro-count red totalItems">{{ $totalCartItems }}</span>
                  </a>
                  <a class="header-action-item header-hamburger ms-4 d-lg-none" href="#drawer-menu"
                     data-bs-toggle="offcanvas">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-hamburger" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                  </a>
               </div>
            </div>
         </div>
      </div>
      <div class="search-wrapper">
         <div class="container">
            <form action="{{ route('searchproduct') }}" method="get" class="search-form d-flex align-items-center">
                <button type="submit" class="search-submit bg-transparent pl-0 text-start">
                 <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-search" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M7.75 0.250183C11.8838 0.250183 15.25 3.61639 15.25 7.75018C15.25 9.54608 14.6201 11.1926 13.5625 12.4846L19.5391 18.4611L18.4609 19.5392L12.4844 13.5627C11.1924 14.6203 9.5459 15.2502 7.75 15.2502C3.61621 15.2502 0.25 11.884 0.25 7.75018C0.25 3.61639 3.61621 0.250183 7.75 0.250183ZM7.75 1.75018C4.42773 1.75018 1.75 4.42792 1.75 7.75018C1.75 11.0724 4.42773 13.7502 7.75 13.7502C11.0723 13.7502 13.75 11.0724 13.75 7.75018C13.75 4.42792 11.0723 1.75018 7.75 1.75018Z" fill="black"/></svg>
               </button>
               <div class="search-input mr-4">
                  <input type="text" placeholder="Search your products..." name="keyword" autocomplete="off" minlength="3" required>
               </div>
               <div class="search-close">
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-close"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
               </div>
            </form>
         </div>
      </div>
   </div>
</header>
<!-- header end -->
<div class="offcanvas offcanvas-start d-flex d-lg-none" tabindex="-1" id="drawer-menu"
   style="visibility: hidden;" aria-hidden="true">
   <div class="offcanvas-wrapper">
      <div class="offcanvas-header border-btm-black">
         <h5 class="drawer-heading">Menu</h5>
         <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"/>
      </div>
      <div class="offcanvas-body p-0 d-flex flex-column justify-content-between">
         <nav class="site-navigation">
            <ul class="main-menu list-unstyled">
               <li class="menu-list-item nav-item has-dropdown active">
                  <div class="mega-menu-header">
                     <a class="nav-link active" href="{{ route('home') }}">  Home </a>
                  </div>
               </li>
               <li class="menu-list-item nav-item has-megamenu">
                  <div class="mega-menu-header">
                     <a class="nav-link" href="{{ route('home') }}">  Shop </a>
                     <span class="open-submenu">
                        <span class="open-submenu">
                          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-dropdown" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                        </span>
                     </span>
                  </div>
                  <div class="submenu-transform submenu-transform-desktop">
                     <div class="container">
                        <div class="offcanvas-header border-btm-black">
                           <h5 class="drawer-heading btn-menu-back d-flex align-items-center">
                              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-menu-back" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                              <span class="menu-back-text">Shop</span>
                           </h5>
                        </div>
                        @if(!empty($categories))
                        <ul class="submenu megamenu-container list-unstyled">
                           @foreach($categories as $category)
                           <li class="menu-list-item nav-item-sub">
                              <div class="mega-menu-header">
                                 <a class="nav-link-sub nav-text-sub megamenu-heading"  href="{{ url( $category['url']) }}">
                                 {{  $category['category_name'] }}
                                 </a>
                                 <span class="open-submenu">
                                    <span class="open-submenu">
                                       <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-dropdown" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                                    </span>
                                 </span>
                              </div>
                              <div class="submenu-transform">
                                 <div class="offcanvas-header border-btm-black">
                                    <h5
                                       class="drawer-heading btn-menu-back d-flex align-items-center">
                                      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-menu-back" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                       <span class="menu-back-text">{{  $category['category_name'] }}</span>
                                    </h5>
                                 </div>
                                 @if(count($category['subcategories'])>0)
                                 <ul class="megamenu list-unstyled megamenu-container">
                                    @foreach($category['subcategories'] as $subcategory)
                                    <li class="menu-list-item nav-item-sub">
                                       <a class="nav-link-sub nav-text-sub" href="{{ url($subcategory['url']) }}">{{ $subcategory['category_name'] }}</a>
                                    </li>
                                    @endforeach
                                 </ul>
                                 @endif
                              </div>
                           </li>
                           @endforeach
                        </ul>
                        @endif
                     </div>
                  </div>
               </li>
               <li class="menu-list-item nav-item"><a class="nav-link" href="{{ route('newarrival') }}">New Arrivals</a></li>
               <li class="menu-list-item nav-item"><a class="nav-link" href="javascript:;">Collections</a></li>
               <li class="menu-list-item nav-item"><a class="nav-link" href="{{ route('contactus') }}">Contact</a></li>
            </ul>
         </nav>
         <ul class="utility-menu list-unstyled">
            <li class="utilty-menu-item">
               <a class="announcement-text" href="tel:+1-078-2376">
                  <span class="utilty-icon-wrapper">
                     <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-phone" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#000" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"> </path></svg>
                  </span>
                  Call: +91 83329 83000
               </a>
            </li>
            <li class="utilty-menu-item">
               <a class="announcement-login announcement-text" @if(Auth::check()) href="{{ route('account',['profile']) }}" @else href="{{ route('signin') }}" @endif>
               <span class="utilty-icon-wrapper">
                  <svg class="icon icon-user" width="24" height="24" viewBox="0 0 10 11" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M5 0C3.07227 0 1.5 1.57227 1.5 3.5C1.5 4.70508 2.11523 5.77539 3.04688 6.40625C1.26367 7.17188 0 8.94141 0 11H1C1 8.78516 2.78516 7 5 7C7.21484 7 9 8.78516 9 11H10C10 8.94141 8.73633 7.17188 6.95312 6.40625C7.88477 5.77539 8.5 4.70508 8.5 3.5C8.5 1.57227 6.92773 0 5 0ZM5 1C6.38672 1 7.5 2.11328 7.5 3.5C7.5 4.88672 6.38672 6 5 6C3.61328 6 2.5 4.88672 2.5 3.5C2.5 2.11328 3.61328 1 5 1Z"fill="#000"></path> </svg>
               </span>
               <span>@if(Auth::check()) Account @else Login @endif</span>
               </a>
            </li>
            <li class="utilty-menu-item">
               <a class="header-action-item header-wishlist" @if(Auth::check()) href="{{ route('account',['wishlist']) }}" @else href="{{ route('signin') }}" @endif>
               <span class="utilty-icon-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-wishlist" width="26" height="22" viewBox="0 0 26 22" fill="#000"><path d="M6.96429 0.000183105C3.12305 0.000183105 0 3.10686 0 6.84843C0 8.15388 0.602121 9.28455 1.16071 10.1014C1.71931 10.9181 2.29241 11.4425 2.29241 11.4425L12.3326 21.3439L13 22.0002L13.6674 21.3439L23.7076 11.4425C23.7076 11.4425 26 9.45576 26 6.84843C26 3.10686 22.877 0.000183105 19.0357 0.000183105C15.8474 0.000183105 13.7944 1.88702 13 2.68241C12.2056 1.88702 10.1526 0.000183105 6.96429 0.000183105ZM6.96429 1.82638C9.73912 1.82638 12.3036 4.48008 12.3036 4.48008L13 5.25051L13.6964 4.48008C13.6964 4.48008 16.2609 1.82638 19.0357 1.82638C21.8613 1.82638 24.1429 4.10557 24.1429 6.84843C24.1429 8.25732 22.4018 10.1584 22.4018 10.1584L13 19.4036L3.59821 10.1584C3.59821 10.1584 3.14844 9.73397 2.69866 9.07411C2.24888 8.41426 1.85714 7.55466 1.85714 6.84843C1.85714 4.10557 4.13867 1.82638 6.96429 1.82638Z" fill="#000"/></svg>
               </span>
               <span>My Wishlist</span>
               </a>
            </li>
         </ul>
      </div>
   </div>
</div>