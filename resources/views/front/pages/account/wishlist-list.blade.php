    @if(!empty($wishlists))
	@foreach($wishlists as $wishlist)
                     @php 
						  $product = $wishlist['product'];
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
					<div class="col-lg-4 col-6" data-aos="fade-up" data-aos-duration="700">
                        <div class="product-card">
                            <div class="product-card-img">
                                <a class="hover-switch" href="javascript:;">
                                    <img class="secondary-img" src="{{ asset($secondaryimg)}}"
                                        alt="product-img">
                                    <img class="primary-img" src="{{ asset($primaryimg) }}" alt="product-img">
                                </a>

                                <div class="product-badge">
                                    <a href="javascript:;" data-id="{{ $wishlist['id'] }}" class="wishlist-delete"><span class="badge-label rounded"><i class="fa-solid fa-xmark"></i></span></a>
                                </div>

                                <div class="product-card-action product-card-action-2 justify-content-center">

                                    <a href="javascript:;" data-id="{{ $wishlist['id'] }}" class="action-card action-wishlist wishlist-delete">
                                    Delete
                                    </a>

                                    <a href="javascript:;" data-wishlist_id="{{ $wishlist['id'] }}"   data-product_id="{{ $product['id'] }}" data-size="{{ $wishlist['size'] }}" class="action-card action-addtocart">
                                        Add To Cart
                                    </a>
                                </div>
                            </div>
                            <div class="product-card-details">
                                <h3 class="product-card-title">
                                    <a href="{{  $product_url  }}">{{ $product['product_name'] }}</a>
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
					  <div class="cart-empty-area text-center">
							 <div class="cart-empty-icon">
								 <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
									 <circle cx="12" cy="12" r="10"></circle>
									 <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
									 <line x1="9" y1="9" x2="9.01" y2="9"></line>
									 <line x1="15" y1="9" x2="15.01" y2="9"></line>
								 </svg>
							 </div>
							 <p class="cart-empty">You have no items in your wishlists</p>
                   </div>
				   
				@endif