  
  @if(!empty($productdetails['relatedproducts']))
  <!-- featured collection start -->
    <div class="featured-collection-section mt-5 mb-5 home-section overflow-hidden ">
        <div class="container-fluid">
            <div class="section-header text-center">
                <p class="section-subheading">WHAT'S NEW</p>
                <h2 class="section-heading">You may also like</h2>
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
					     if(count($productdetails['relatedproducts']) > 1){ 
						     $relatedproducts = $productdetails['relatedproducts'];
					     }else{
							 $relatedproducts = array_merge($productdetails['relatedproducts'],$productdetails['relatedproducts']);
                            
						 }
					@endphp
					
					
					@foreach($relatedproducts as $product)
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
                                    <a href="javascript:;" data-id="{{ $product['id'] }}" class="action-card action-quickview product-quick-view"><i class="fas fa-search-plus"></i> </a>
                                    <a @if(Auth::check()) href="{{ $product_url }}"  @else href="{{ route('signin') }}" @endif class="action-card action-wishlist"> <i class="far fa-heart"></i></a>
                                    <a href="{{ $product_url }}" class="action-card action-addtocart"> <i class="fas fa-shopping-bag"></i> </a>
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
	