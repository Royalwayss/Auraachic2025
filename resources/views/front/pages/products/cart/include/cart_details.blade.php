@php use App\Models\Coupon; @endphp

@if(!empty($cartitems['items']))
					<div class="col-lg-8 col-md-12 col-12">
                        <table class="cart-table w-100">
                            <thead>
                                <tr>
                                    <th class="cart-caption heading_18" >Product</th>
                                    <th class="cart-caption heading_18" style="width:40%"></th>
                                    <th class="cart-caption text-center heading_18 d-none d-md-table-cell">Qty</th>
                                    <th class="cart-caption text-end heading_18">Price</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($cartitems['items'] as $cartitem)
								@php $priceDetails = $cartitem['priceDetails'];  @endphp 
								<tr class="cart-item">
                                    <td class="cart-item-media">
                                        <div class="mini-img-wrapper">
												<a href="{{ route('product',[$cartitem['product']['product_url'],$cartitem['product']['id']]) }}">
												@if(!empty($cartitem['product']['product_image']['image']))
												   <img class="mini-img" src="{{ asset('front/images/products/small/'.$cartitem['product']['product_image']['image'])}}"" alt="img">
												 @else
													<img src="{{asset('front/images/no-image-found.jpg')}}" alt="{{$cartitem['product']['product_name']}}" class="product-image">
												 @endif	
												</a>												 
                                        </div>
										
                                    </td>
                                    <td class="cart-item-details">
                                        <h2 class="product-title"><a href="{{ route('product',[$cartitem['product']['product_url'],$cartitem['product']['id']]) }}">{{ $cartitem['product']['product_name'] }}</a></h2>
                                        <p class="product-vendor">Size: {{ $cartitem['size'] }}</p>
                                    </td>
                                    <td class="cart-item-quantity">
                                        <div class="quantity d-flex align-items-center justify-content-between">
                                            <button class="qty-btn dec-qty changeqty" data-size="{{ $cartitem['size'] }}" data-proid="{{ $cartitem['product_id'] }}" data-cartid="{{ $cartitem['id'] }}"><img src="{{ asset('front/assets/img/icon/minus.svg') }}"
                                                    alt="minus"></button>
                                            <input class="qty-input"  type="number" id="qty-{{ $cartitem['id'] }}" name="qty" value="{{ $cartitem['qty'] }}" min="0">
                                            <button class="qty-btn inc-qty changeqty" data-size="{{ $cartitem['size'] }}" data-proid="{{ $cartitem['product_id'] }}" data-cartid="{{ $cartitem['id'] }}"><img src="{{ asset('front/assets/img/icon/plus.svg') }}"
                                                    alt="plus"></button>
                                        </div>
                                        <a href="javascript:;" data-cartid="{{ $cartitem['id'] }}" class="deleteCartItem product-remove mt-2">Remove</a>
                                    </td>
                                    <td class="cart-item-price text-end">
                                        <div class="product-price">
										₹{{ amount_format($priceDetails['subtotal']) }}
										@if(!empty($cartitem['product']['product_gst']))
										<br>Included GST {{ $cartitem['product']['product_gst'] }}%
										@endif
										</div>
                                    </td>
                                </tr>
                                @endforeach
							</tbody>
                        </table>
                    </div>
                    <div class="col-lg-4 col-md-12 col-12">
                        <div class="cart-total-area">
                            <div class="coupon field_form input-group mb-3">
                                <input type="text" id="couponCode" name="coupon" @if(Session::has('couponinfo') && !empty(Session::get('couponinfo')['coupon_code'])) value="{{ Session::get('couponinfo')['coupon_code'] }}" @endif
                                    class="form-control form-control-sm"
                                    placeholder="Enter Coupon Code / Credit Amount">
                                
                                <div class="input-group-append">
                                    <button class=" primaryBtn borderBtn" type="button" value="Apply"
                                        id="CouponBtn">APPLY</button>
                                </div>
                                
                            </div>
                           <?php /* <p class="credit-limit">Available Credits: <strong>₹500.00</strong></p>*/ ?>
                            @php  $availableCoupons = Coupon::availableCoupons($cartitems);  @endphp
							@if(!empty($availableCoupons))
							<h3 class="cart-total-title d-none d-lg-block mb-2">Available Coupons</h3>
                            <div class="couponCard cart-total-box ">
                                @foreach($availableCoupons as $key=>$available_coupon) 
								<div class="couponHeader">
                                    <span class="couponTitle">Code: <strong class="couponCode coupon-key{{$key}}">{{ $available_coupon['coupon_code'] }}</strong></span>
                                     @if(Session::has('couponinfo') && Session::get('couponinfo')['coupon_code'] ==  $available_coupon['coupon_code'])
										 
									       <button class="copyBtn btn-success"  disabled="">
										         <i class="fa fa-check"></i>
										   </button>
									 @else
								     <button class="copyBtn" data-key="{{$key}}">
                                        <i class="fa fa-copy"></i>
                                    </button>
									@endif
                                </div>
                                <ul class="couponDetails">
                                    <li>Coupon Discount:<strong><span class="highlightDiscount"> {{ $available_coupon['amount'] }}@if($available_coupon['amount_type'] == 'Percentage')%@else Rs @endif Off
                                            </span></strong>
                                    </li>
                                    <li>Coupon Validity:<strong> {{date('d-m-Y', strtotime($available_coupon['expiry_date']))}}</strong> </li>
                                </ul>
                                <!--<p class="copyMsg w100" style="display:none">Code copied!</p> -->
								@endforeach
                            </div>
                            @endif

                            @php $summery = $cartitems['cartPricing']; @endphp
                            <h3 class="cart-total-title d-none d-lg-block mb-0 mt-3">Cart Totals</h3>
                            <div class="cart-total-box mt-2">
                                <div class="subtotal-item subtotal-box">
                                    <h4 class="subtotal-title">Sub Totals:</h4>
                                    <p class="subtotal-value">₹{{ amount_format($summery['subtotal']) }}</p>
                                </div>
                                <div class="subtotal-item shipping-box">
                                    <h4 class="subtotal-title">Shipping:</h4>
                                    <p class="subtotal-value"><strong>@if(!empty( $summery['shipping'])) ₹{{ amount_format($summery['shipping']) }} @else Free @endif</strong></p>
                                </div>
								@if(!empty($summery['discount']))
                                <div class="subtotal-item discount-box">
                                    <h4 class="subtotal-title">Discount:</h4>
                                    <p class="subtotal-value">₹{{ amount_format($summery['discount']) }}</p>
                                </div>
								@endif
                                <hr>
                                <div class="subtotal-item discount-box">
                                    <h4 class="subtotal-title">Total:</h4>
                                    <p class="subtotal-value">₹{{ amount_format($summery['grandtotal']) }}</p>
                                </div>
                                <!-- <p class="shipping_text">Shipping &amp; taxes calculated at checkout</p> -->
                                <div class="d-flex justify-content-center mt-4">
                                    <a href="{{ route('checkout') }}" class="position-relative btn-primary text-uppercase">
                                        Procced to checkout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
					@else
						<div class="col-12">
					    <div class="text-align">
							<img class="w100" src="{{ asset('front/assets/img/shopping-cart.png') }}">
							<h5 class="mt-5">Your shopping cart is currently empty. <br>Start <a href="{{ route('home') }}">Shopping</a> to fill it up!</h5>
						</div>
						</div>
                    @endif
				