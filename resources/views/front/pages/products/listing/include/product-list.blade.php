<div class="collection-product-container" >
   <div class="row">
      @if(count($products)>0)
      @foreach($products as $key=> $product)   
      @php 

      $primaryimg =  'front/images/products/medium/'.$product['main_image'] ?? '';
      $secondaryimg = isset($product['product_image']['image']) ? 'front/images/products/medium/'.$product['product_image']['image'] : '';
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
      <div class="col-lg-4 col-md-6 col-6" @if(!isset($ajax_call)) data-aos="fade-up" data-aos-duration="700" @endif>
         <div class="product-card">
            <div class="product-card-img">
               <a class="hover-switch" href="{{ $product_url }}">
               <img class="secondary-img" src="{{ $secondaryimg }}"
                  alt="product-img">
               <img class="primary-img" src="{{ $primaryimg }}" alt="product-img">
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
                  <a href="javascript:;" data-id="{{ $product['id'] }}" class="action-card action-quickview product-quick-view"><i class="fas fa-search-plus"></i> </a>
                  <a @if(Auth::check()) href="{{ $product_url }}"  @else href="{{ route('signin') }}" @endif class="action-card action-wishlist addWishList" data-productid="{{$product['id']}}" ><i class="far fa-heart"></i> </a>
                  <a href="{{ $product_url }}" class="action-card action-addtocart"> <i class="fas fa-shopping-bag"></i></a>
               </div>
            </div>
            <div class="product-card-details">
               <!-- <ul class="color-lists list-unstyled d-flex align-items-center">
                  <li><a href="javascript:void(0)"
                          class="color-swatch swatch-black active"></a></li>
                  <li><a href="javascript:void(0)" class="color-swatch swatch-cyan"></a></li>
                  <li><a href="javascript:void(0)" class="color-swatch swatch-purple"></a>
                  </li>
                  </ul> -->
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
	  @else     
	    <h2 class="product-title mb-3 mt-50">We’re working on adding amazing products here—come back soon!</h2>
      @endif
   </div>
</div>
@if(isset($pagination_links) && count($pagination_links) > 3)
<div class="pagination justify-content-center">
   <nav> 
      <ul class="pagination m-0 d-flex align-items-center">
         @foreach($pagination_links as $pagination_link)
		 @if($pagination_link['label'] == '&laquo; Previous')
		 <li class="item disabled">
            <a class="link">
               <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" class="icon icon-left">
                  <polyline points="15 18 9 12 15 6"></polyline>
               </svg>
            </a>
         </li>
		 @endif
		 @if(is_numeric($pagination_link['label']))
         <li class="item @if($pagination_link['active'] =='1') active disabled @endif" ><a class="link" @if($pagination_link['active'] =='1') href="javascript:;" @else href="{{ $pagination_link['url'] }}" @endif>{{ $pagination_link['label'] }}</a></li>
         @endif
		
		 @if($pagination_link['label'] == 'Next &raquo;')
         <li class="item">
            <a class="link" href="{{ $pagination_link['url'] }}">
               <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100"
                  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                  stroke-linecap="round" stroke-linejoin="round" class="icon icon-right">
                  <polyline points="9 18 15 12 9 6"></polyline>
               </svg>
            </a>
         </li>
		 @endif
		 @endforeach
      </ul>
   </nav>
</div>
@endif