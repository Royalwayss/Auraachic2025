
         <div class="offcanvas-header border-btm-black">
             <h5 class="cart-drawer-heading text_16">Your Cart ({{ $cartitems['totalCartItems'] }})</h5>
             <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
         </div>
         <div class="offcanvas-body p-0">
             @if(!empty($cartitems['totalCartItems']))
			 <div class="cart-content-area d-flex justify-content-between flex-column">
                 <div class="minicart-loop custom-scrollbar">
                     @foreach($cartitems['items'] as $cartitem)
					  @php $priceDetails = $cartitem['priceDetails'];  @endphp 
					 <!-- minicart item -->
                     <div class="minicart-item d-flex">
                         <div class="mini-img-wrapper">
						 <a href="{{ route('product',[$cartitem['product']['product_url'],$cartitem['product']['id']]) }}">
						 @if(!empty($cartitem['product']['product_image']['image']))
                             <img class="mini-img" src="{{ asset('front/images/products/small/'.$cartitem['product']['product_image']['image'])}}"" alt="img">
						 @else
                            <img src="{{asset('front/images/no-image-found.jpg')}}" alt="{{$cartitem['product']['product_name']}}" class="product-image">
                         @endif	 
						 </a>
                         </div>
                         <div class="product-info">
                             <h2 class="product-title"><a href="{{ route('product',[$cartitem['product']['product_url'],$cartitem['product']['id']]) }}">{{ $cartitem['product']['product_name'] }}</a></h2>
                             <p class="product-vendor">{{ $cartitem['size'] }}</p>
                             <div class="misc d-flex align-items-end justify-content-between">
                                 <div class="quantity d-flex align-items-center justify-content-between">
                                            <button class="qty-btn dec-qty changeqty" data-size="{{ $cartitem['size'] }}" data-proid="{{ $cartitem['product_id'] }}" data-cartid="{{ $cartitem['id'] }}"><img src="{{ asset('front/assets/img/icon/minus.svg') }}"
                                                    alt="minus"></button>
                                            <input class="qty-input"  type="number" id="qty-{{ $cartitem['id'] }}" name="qty" value="{{ $cartitem['qty'] }}" min="0">
                                            <button class="qty-btn inc-qty changeqty" data-size="{{ $cartitem['size'] }}" data-proid="{{ $cartitem['product_id'] }}" data-cartid="{{ $cartitem['id'] }}"><img src="{{ asset('front/assets/img/icon/plus.svg') }}"
                                                    alt="plus"></button>
                                        </div>
                                 <div class="product-remove-area d-flex flex-column align-items-end">
                                     <div class="product-price">₹{{ $priceDetails['subtotal'] }}</div>
                                     <a href="javascript:;" data-cartid="{{ $cartitem['id'] }}" class="deleteCartItem product-remove mt-2">Remove</a>
                                 </div>
                             </div>
                         </div>
                     </div>
                     <!-- minicart item -->
                     @endforeach
				 
				 </div>
				 @php $summery = $cartitems['cartPricing']; @endphp
                 <div class="minicart-footer">
                     <div class="minicart-calc-area">
                         <div class="minicart-calc d-flex align-items-center justify-content-between">
                             <span class="cart-subtotal mb-0">Subtotal</span>
                             <span class="cart-subprice">₹{{ $summery['subtotal'] }}</span>
                         </div>
                         <p class="cart-taxes text-center my-4">Taxes and shipping will be calculated at checkout.
                         </p>
                     </div>
                     <div class="minicart-btn-area d-flex align-items-center justify-content-between">
                         <a href="{{ route('cart') }}" class="minicart-btn btn-secondary">View Cart</a>
                         <a href="{{ route('checkout') }}" class="minicart-btn btn-primary">Checkout</a>
                     </div>
                 </div>
             </div>
			 @else
             <div class="cart-empty-area text-center py-5">
                 <div class="cart-empty-icon pb-4">
                     <svg xmlns="http://www.w3.org/2000/svg" width="70" height="70" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                         <circle cx="12" cy="12" r="10"></circle>
                         <path d="M16 16s-1.5-2-4-2-4 2-4 2"></path>
                         <line x1="9" y1="9" x2="9.01" y2="9"></line>
                         <line x1="15" y1="9" x2="15.01" y2="9"></line>
                     </svg>
                 </div>
                 <p class="cart-empty">You have no items in your cart</p>
             </div>
			 @endif
         </div>
     
	 
     