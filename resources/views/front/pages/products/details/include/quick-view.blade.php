 <div class="row">
                         <div class="col-lg-6 col-md-12 col-12">
                             <div class="product-gallery product-gallery-vertical d-flex">
                                 <div class="product-img-large">
                                     <div class="qv-large-slider img-large-slider common-slider" data-slick='{
                                            "slidesToShow": 1, 
                                            "slidesToScroll": 1,
                                            "dots": false,
                                            "arrows": false,
                                            "asNavFor": ".qv-thumb-slider"
                                        }'>
                                         @foreach($productdetails['productimages'] as $productimage)
										 <div class="img-large-wrapper">
                                             <img src="{{ asset('front/images/products/large/'.$productimage['image']) }}" alt="img">
                                         </div>
                                        
                                         @endforeach
                                     </div>
                                 </div>
                                 <div class="product-img-thumb">
                                     @php  
										 if(count($productdetails['productimages']) > 1){
											 $slidesToShow = count($productdetails['productimages'])-1;
										 }else{
											 $slidesToShow = 4;
										 }
									 @endphp
									   
									 <div class="qv-thumb-slider img-thumb-slider common-slider"
                                         data-vertical-slider="true" data-slick='{
										 "slidesToShow": {{ $slidesToShow }}, 
                                            "slidesToScroll": 1,
                                            "dots": false,
                                            "arrows": true,
                                            "infinite": false,
                                            "speed": 300,
                                            "cssEase": "ease",
                                            "focusOnSelect": true,
                                            "swipeToSlide": true,
                                            "asNavFor": ".qv-large-slider"
                                        }'>
                                         
										  @foreach($productdetails['productimages'] as $productimage)
										 <div>
                                             <div class="img-thumb-wrapper">
                                                 <img src="{{ asset('front/images/products/large/'.$productimage['image']) }}" alt="img">
                                             </div>
                                         </div>
                                        
                                          @endforeach
                                        
                                        
                                         
                                     </div>
                                     <div
                                         class="activate-arrows show-arrows-always arrows-white d-none d-lg-flex justify-content-between mt-3">
                                     </div>
                                 </div>
                             </div>
                         </div>
                         <div class="col-lg-6 col-md-12 col-12">
                             <div class="product-details ps-lg-4">

                                 <h2 class="product-title mb-3">{{$productdetails['product_name']}}</h2>
                                
								  <div class="product-price-wrapper mb-2" id="pro_price_details">
										MRP
										<span class="product-price regular-price product_final_price">₹{{round($productdetails['final_price'],2)}}</span>
										@if($productdetails['product_discount']>0)
										<del class="product-price compare-price">₹{{round($productdetails['product_price'],2)}}</del>
										@endif
									   @if($productdetails['product_discount']>0)
										<span class="text-include">{{ $productdetails['product_discount'] }}% off (Incl. of all taxes)</span>
									   @endif
                                 </div>
                                @if(!empty($avgStarRating))
								  <div class="product-rating d-flex align-items-center mb-3">
									 <span class="star-rating">
									 @for($i=0; $i<$avgStarRating; $i++)
									  <i class="fa-solid fa-star"></i>
									 @endfor
									 
									 </span>
									 <span class="rating-count ms-2">({{ $ratingCount }})</span>
								  </div>
								@endif

                                 <div class="product-sku product-meta mb-1">
                                     <strong class="label">Product Code:</strong> {{$productdetails['product_code']}}
                                 </div>

                                 <hr>

                                 <div class="product-variant-wrapper">
                                     @if(!empty(count($productdetails['groups'])))
									 <div class="product-variant product-variant-color">
                                         <strong class="label mb-1 d-block">Color:</strong>
                                        
                                         <ul class="variant-list list-unstyled d-flex align-items-center flex-wrap">
                                               @foreach($productdetails['groups'] as $group) 
											 <li class="variant-item product-quick-view" data-is_modal_open="1" data-id="{{ $group['id'] }}">
                                                 <input type="radio" value="cyan" @if($group['id'] == $productdetails['id'])  checked @endif>
                                                 <label class="variant-label" style="background-color:{{ $group['family_color'] }}"></label>
                                             </li>
                                             @endforeach
                                         </ul>
                                     </div>
                          
                                     <hr>
                                     @endif
                                     <div class="product-variant product-variant-other">
                                         <strong class="label mb-1 d-block">Size:</strong>
                                            <ul class="variant-list list-unstyled d-flex align-items-center flex-wrap">
												@php $total_stock =0; @endphp
												@if(count($productdetails['attributes'])>0) 
												@foreach ($productdetails['attributes'] as $key => $attribute)
												 @php $total_stock +=$attribute['stock']; @endphp
												<li data-type="pro" class="variant-item @if(!empty($attribute['stock'])) product_size @endif" data-size="{{ $attribute['size'] }}" data-product_id="{{ $productdetails['id'] }}" data-key="{{ $key }}">
													<input type="radio" class="chkPrice" name="product_size" id="pro-size-{{ $key }}"   value="{{ $attribute['size'] }}" @if(empty($attribute['stock'])) disabled @endif  >
													<label class="variant-label">{{ $attribute['size'] }}</label>
												</li>
												@endforeach
												@endif
											</ul>
                                     </div>
                                 
								 
								 </div>
                                      <form class="product-form" action="javascript:;" id="QucikViewAddtoCart">@csrf 
									  
									  <input type="hidden" name="size" id="pro_size">
									  <input type="hidden" name="product_id" id="product_id" value="{{ $productdetails['id'] }}">
									 <div class="misc d-flex align-items-center justify-content-between product_quantity mt-4">
											<div class="quantity d-flex align-items-center justify-content-between product_quantity">
												<button class="qty-btn dec-qty"><img src="{{ asset('front/assets/img/icon/minus.svg') }}"
														alt="minus"></button>
												<input class="qty-input" type="number" id="pro_qty" name="qty" value="1" min="0">
												<button class="qty-btn inc-qty"><img src="{{ asset('front/assets/img/icon/plus.svg') }}" alt="plus"></button>
											</div>
											 <div>
											   @if(!empty($total_stock)) 
											   <span class="product-availability d-flex align-item-center">In Stock</span>
											   @else
											   <span class="product-availability d-flex align-item-center">Out of Stock</span>
											   @endif
										  </div> 
									</div>

                                  
								 
									 <div class="product-form-buttons d-flex align-items-center justify-content-between mt-4">
                                         <button data-cart-type="popup" type="submit" class="position-relative btn-atc btn-add-to-cart loader add-to-cart" data-type="cart">ADD TO CART</button>
                                         <button data-cart-type="popup" type="submit" class="position-relative btn-atc btn-buyit-now add-to-cart" data-type="buy">BUY IT NOW</button>
                                         <a data-cart-type="popup" href="javascript:;"  data-product-id="{{ $productdetails['id'] }}" class="product-wishlist pro-wishlist-btn"><i class="fa-regular fa-heart"></i> </a>
                                     </div>
                                 </form>
                             </div>
                         </div>
                     </div>
                 