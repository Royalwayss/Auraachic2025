@extends('front.layout.layout')
@section('content')
<main id="MainContent" class="content-for-layout">
   @if(!empty($top_banners))
   <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
      <div class="carousel-indicators">
         @foreach($top_banners as $key=>$banner)
         <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $key }}" @if($key == 0) class="active" @endif aria-current="true" aria-label="Slide {{ $key }}"></button>
         @endforeach
      </div>
      <div class="carousel-inner">
         @foreach($top_banners as $key=>$banner)
         <div class="carousel-item pointer @if($key == 0) active @endif" @if(!empty($banner['link'])) onclick="window.location.href='{{ url($banner['link']) }}'" @endif >
         <img class="slide-img d-none d-md-block  w-100" src="{{ asset('front/images/banners/'.$banner['image']) }}" title="{{ $banner['title'] }}" alt="{{ $banner['alt'] }}">
         <img class="slide-img d-md-none  w-100" src="{{ asset('front/images/banners/'.$banner['mobile_banner']) }}" title="{{ $banner['title'] }}" alt="{{ $banner['alt'] }}">
      </div>
      @endforeach
   </div>
   <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
   <span class="carousel-control-prev-icon" aria-hidden="true"></span>
   <span class="visually-hidden">Previous</span>
   </button>
   <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
   <span class="carousel-control-next-icon" aria-hidden="true"></span>
   <span class="visually-hidden">Next</span>
   </button>
   </div>
   @endif
   @if(!empty($featured_products))
   <!-- collection start -->
   <div class="featured-collection overflow-hidden">
      <div class="collection-tab-inner">
         <div class="container-fluid">
            <div class="section-header text-center">
               <p class="section-subheading">WHAT'S NEW</p>
               <h2 class="section-heading">Shop by Occasion</h2>
            </div>
            <div class="row">
               @foreach($featured_products as $product)
				    @php 
					   $primaryimg =  'front/images/products/medium/'.$product['main_image'] ?? '';
					   $secondaryimg = isset($product['productimages'][0]['image']) ? 'front/images/products/medium/'.$product['productimages'][0]['image'] : '';
					   $fallbackImage = 'front/images/no-image-found.jpg';
					   if(empty($primaryimg) || !File::exists(public_path($primaryimg))){
							$primaryimg = $secondaryimg;
					   }
					   if(empty($primaryimg) || !File::exists(public_path($primaryimg))){
							$primaryimg = $fallbackImage;
					   }
					   if(empty($secondaryimg) || !File::exists(public_path($secondaryimg))){
							$secondaryimg = $primaryimg;
					   }
					   $product_url = route('product',[$product['product_url'],$product['id']]);
                    @endphp
               <div class="col-lg-4 col-md-6 col-6" data-aos="fade-up" data-aos-duration="700">
                  <div class="product-card">
                     <div class="product-card-img">
                        <a class="hover-switch" href="{{ $product_url }}">
                        <img class="secondary-img" src="{{ $secondaryimg }}" alt="product-img"  title="product-img">
                        <img class="primary-img" src="{{ $primaryimg }}" alt="product-img" title="product-img">
                        </a>
                        <div class="product-badge">
                           @if($product['is_featured']=="Yes")
                           <span class="badge-label badge-new rounded">Featured</span>
                           @endif
                           @if($product['product_discount']>0)
                           <span class="badge-label badge-percentage rounded">-{{$product['product_discount']}}%</span>
                           @endif
                        </div>
                        <div class="product-card-action product-card-action-2 justify-content-center">
                           <a href="javascript:;"  data-id="{{ $product['id'] }}" class="action-card action-quickview product-quick-view"><i class="fa-solid fa-magnifying-glass-plus"></i></a>
                           <a @if(Auth::check()) href="{{ $product_url }}"  @else href="{{ route('signin') }}" @endif class="action-card action-wishlist"> <i class="far fa-heart"></i></a>
                           <a href="{{ $product_url }}" class="action-card action-addtocart">
                           <i class="fas fa-shopping-bag"></i>
                           </a>
                        </div>
                     </div>
                     <div class="product-card-details">
                        <h3 class="product-card-title">
                           <a href="{{ $product_url }}">{{$product['product_name']}}</a>
                        </h3>
                        <div class="product-card-price">
                           <span class="card-price-regular">₹{{$product['final_price']}} </span>
                           @if($product['discount_type'] !="")
                           <span class="card-price-compare text-decoration-line-through">₹{{$product['product_price']}}</span>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               @endforeach
            </div>
            <div class="view-all text-center" data-aos="fade-up" data-aos-duration="700">
               <a class="btn-primary" href="{{ url('new-arrival') }}">VIEW ALL</a>
            </div>
         </div>
      </div>
   </div>
   <!-- collection end -->
   @endif
   @if(!empty($category_banners))
   <!-- banner start -->
   <div class="banner-section mt-100 overflow-hidden">
      <div class="banner-section-inner">
         <div class="container-fluid">
            <div class="row justify-content-center">
               @foreach($category_banners as $key=>$banner)
               <div class="col-lg-6 col-md-6 col-12" data-aos="fade-right" data-aos-duration="1200">
                  <a class="banner-item position-relative rounded" @if(!empty($banner['link'])) href="{{ $banner['link'] }}" @else href="javascript:;"@endif>
                  <img class="banner-img" src="{{ asset('front/images/banners/'.$banner['image']) }}" title="{{ $banner['title'] }}" alt="{{ $banner['alt'] }}">
                  </a>
               </div>
               @endforeach
            </div>
         </div>
      </div>
   </div>
   <!-- banner end -->
   @endif
   @if(!empty($newarrival_products))
   <!-- featured collection start -->
   <div class="featured-collection-section mt-100 home-section overflow-hidden">
      <div class="container-fluid">
         <div class="section-header text-center">
            <p class="section-subheading">WHAT'S NEW</p>
            <h2 class="section-heading">The Latest Drop</h2>
         </div>
         <div class="product-container position-relative">
            <div class="common-slider" data-slick='{
               "slidesToShow": 4, 
               "slidesToScroll": 1,
               "dots": false,
               "arrows": true,
               "responsive": [
               {
               "breakpoint": 1281,
               "settings": {
               "slidesToShow": 3
               }
               },
               {
               "breakpoint": 768,
               "settings": {
               "slidesToShow": 2
               }
               }
               ]
               }'>
               @php  
               if(count($newarrival_products) > 1){ 
				$newarrival_products = $newarrival_products;
               }else{
				$newarrival_products = array_merge($newarrival_products,$newarrival_products);
               }
               @endphp
               @foreach($newarrival_products as $product)
				    @php 
					   $primaryimg =  'front/images/products/medium/'.$product['main_image'] ?? '';
					   $secondaryimg = isset($product['productimages'][0]['image']) ? 'front/images/products/medium/'.$product['productimages'][0]['image'] : '';
					   $fallbackImage = 'front/images/no-image-found.jpg';
					   if(empty($primaryimg) || !File::exists(public_path($primaryimg))){
							$primaryimg = $secondaryimg;
					   }
					   if(empty($primaryimg) || !File::exists(public_path($primaryimg))){
							$primaryimg = $fallbackImage;
					   }
					   if(empty($secondaryimg) || !File::exists(public_path($secondaryimg))){
							$secondaryimg = $primaryimg;
					   }
					   $product_url = route('product',[$product['product_url'],$product['id']]);
                    @endphp
               <div class="new-item" data-aos="fade-up" data-aos-duration="700">
                  <div class="product-card">
                     <div class="product-card-img">
                        <a class="hover-switch" href="{{ $product_url }}">
                        <img class="secondary-img" src="{{ asset($secondaryimg) }}"
                           alt="product-img">
                        <img class="primary-img" src="{{ asset($primaryimg) }}" alt="product-img">
                        </a>
                        <div class="product-badge">
                           @if($product['is_new']=="Yes")
                           <span class="badge-label badge-new rounded">New</span>
                           @endif
                           @if($product['product_discount']>0)
                           <span class="badge-label badge-percentage rounded">-{{$product['product_discount']}}%</span>
                           @endif
                        </div>
                        <div class="product-card-action product-card-action-2 justify-content-center">
                           <a href="javascript:;"  data-id="{{ $product['id'] }}" class="action-card action-quickview product-quick-view"><i class="fa-solid fa-magnifying-glass-plus"></i> </a>
                           <a @if(Auth::check()) href="{{ $product_url }}"  @else href="{{ route('signin') }}" @endif class="action-card action-wishlist"> <i class="far fa-heart"></i></a>
                           <a href="{{ $product_url }}" class="action-card action-addtocart"><i class="fas fa-shopping-bag"></i></a>
                        </div>
                     </div>
                     <div class="product-card-details">
                        <h3 class="product-card-title">
                           <a href="{{ $product_url }}">{{ $product['product_name'] }}</a>
                        </h3>
                        <div class="product-card-price">
                           <span class="card-price-regular">₹{{$product['final_price']}} </span>
                           @if($product['discount_type'] !="")
                           <span class="card-price-compare text-decoration-line-through">₹{{$product['product_price']}}</span>
                           @endif
                        </div>
                     </div>
                  </div>
               </div>
               @endforeach
            </div>
            <div class="activate-arrows show-arrows-always article-arrows arrows-white"></div>
         </div>
      </div>
   </div>
   <!-- featured collection end -->
   @endif
   @if(!empty($single_banner))
   <!-- single banner start -->
   <div class="single-banner-section mt-100 overflow-hidden">
      <div class="position-relative overlay">
         <img class="single-banner-img" src="{{ asset('front/images/banners/'.$single_banner['image']) }}" title="{{ $single_banner['title'] }}" alt="{{ $single_banner['alt'] }}">
      </div>
   </div>
   <!-- single banner end -->
   @endif
   @if(!empty($single_banner))
   <!-- instagram start --> 
   <div class="instagram-section mt-100 overflow-hidden home-section">
      <div class="instagram-inner">
         <div class="container">
            <div class="section-header text-center">
               <h2 class="section-heading">Follow Us</h2>
               <p class="section-subheading text-center">See how our customers styled with our latest trends</p>
               <div class="section-icon followIcon">
                  <a href="https://www.instagram.com/auraachic_in/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                  <a href="https://www.facebook.com/Auraachicofficial/" target="_blank"><i class="fa-brands fa-square-facebook"></i></a>
               </div>
            </div>
            <div class="instagram-container position-relative mt-48">
               <div class="common-slider" data-slick='{
                  "slidesToShow": 4, 
                  "slidesToScroll": 1,
                  "dots": false,
                  "arrows": true,
                  "autoplay":true,
                  "responsive": [
                  {
                  "breakpoint": 1281,
                  "settings": {
                  "slidesToShow": 3
                  }
                  },
                  {
                  "breakpoint": 768,
                  "settings": {
                  "slidesToShow": 2
                  }
                  }
                  ]
                  }'>
                  
				  @foreach($instagram_banners as $instagram_banner)
				  <div class="instagram-slick-item" data-aos="fade-up" data-aos-duration="700">
                     <div class="instagram-card">
                        <a class="instagram-img-wrapper" @if(!empty($banner['link'])) href="{{ $instagram_banner['link'] }}" @else href="javascript:;"@endif>
                        <img src="{{ asset('front/images/banners/'.$instagram_banner['image']) }}" alt="{{ $instagram_banner['alt'] }}" title="{{ $instagram_banner['title'] }}" class="instagram-card-img rounded">
                        </a>
                     </div>
                  </div>
                 @endforeach
                  
               </div>
               <div class="activate-arrows show-arrows-always article-arrows arrows-white"></div>
            </div>
         </div>
      </div>
   </div>
   <!-- instagram end -->
   @endif
   <!-- trusted badge start -->
   <div class="trusted-section mt-100 overflow-hidden">
      <div class="trusted-section-inner">
         <div class="container">
            <div class="row justify-content-center trusted-row">
               <div class="col-lg-4 col-md-6 col-12">
                  <div class="trusted-badge bg-trust-1 rounded">
                     <div class="trusted-icon">
                        <img class="icon-trusted" src="{{ asset('front/assets/img/trusted/1.png') }}" alt="icon-1" title="icon-1">
                     </div>
                     <div class="trusted-content">
                        <h2 class="heading_18 trusted-heading">Free Shipping & Exchange</h2>
                        <p class="text_16 trusted-subheading trusted-subheading-2">On all order</p>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-6 col-12">
                  <div class="trusted-badge bg-trust-2 rounded">
                     <div class="trusted-icon">
                        <img class="icon-trusted" src="{{ asset('front/assets/img/trusted/2.png') }}" alt="icon-2" title="icon-2">
                     </div>
                     <div class="trusted-content">
                        <h2 class="heading_18 trusted-heading">Customer Support 24/7</h2>
                        <p class="text_16 trusted-subheading trusted-subheading-2">Instant access to
                           support
                        </p>
                     </div>
                  </div>
               </div>
               <div class="col-lg-4 col-md-6 col-12">
                  <div class="trusted-badge bg-trust-3 rounded">
                     <div class="trusted-icon">
                        <img class="icon-trusted" src="{{ asset('front/assets/img/trusted/3.png') }}" alt="icon-3" title="icon-3">
                     </div>
                     <div class="trusted-content">
                        <h2 class="heading_18 trusted-heading">100% Secure Payment</h2>
                        <p class="text_16 trusted-subheading trusted-subheading-2">We ensure secure
                           payment!
                        </p>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <!-- trusted badge end -->
</main>
@endsection