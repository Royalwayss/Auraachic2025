 @php
 use App\Models\ShippingAddress; 
 use App\Models\BillingAddress; 
 $shipping_addresses = ShippingAddress::addresses();
 $billing_addresses = BillingAddress::addresses(); 
 @endphp


                       <div class="row">
						   <div class="col-lg-8 col-md-12 col-12">
								       <h2 class="shipping-address-heading pb-1"><strong>Shipping Address</strong></h2>
						   </div>
						   <div class="col-lg-4 col-md-12 col-12 text-right">
										<a href="javascript:;" class="button2 orderAddress" data-type="shipping" data-id="">Add Shipping Address</a>
						   </div>
					   </div>
					   <div class="row">
						@foreach($shipping_addresses as $shipping_address)
						<div class="checkout-user-area overflow-hidden d-flex">
                            <label class="checkout-lable">
                                <input type="radio" @if($shipping_address['is_default'] == '1') checked="checked"  @endif name="default_shipping_address" value="{{ $shipping_address['id'] }}">
                                <span class="checkmark"></span>
                            </label>
                            <div class="checkout-user-details d-flex align-items-center justify-content-between w-100">
                                <div class="checkout-user-info">
                                    <h2 class="checkout-user-name">{{ $shipping_address['name'] }}</h2>
                                    <p class="checkout-user-address mb-0">{{ $shipping_address['address'] }}
                                    </p>
                                </div>
                                <div class="btn-groups">
                                    <a href="javascript:;" class="edit-user btn-secondary orderAddress" data-type="shipping" data-id="{{ $shipping_address['id'] }}" >Edit</a>
                                    @if($shipping_address['is_default'] != '1') 
									<a href="javascript:;"  data-id="{{ $shipping_address['id'] }}" class="edit-user deleteaddress btn-secondary">Remove</a>
                                    @endif
								</div>
                            </div>
                        </div>
                       @endforeach

                        
						</div>
						
						
						<div class="row mt-50">
						   <div class="col-lg-8 col-md-12 col-12">
								       <h2 class="shipping-address-heading pb-1"><strong>Billing Address</strong></h2>
						   </div>
						   @if(empty($billing_addresses))
						   <div class="col-lg-4 col-md-12 col-12 text-right">
										<a href="javascript:;" class="button2 orderAddress" data-type="billing" data-id="">Add Billing Address</a>
						   </div>
						   @endif
					   </div>
						
						
						
						<div class="row">
						@if(!empty($billing_addresses))
						<div class="checkout-user-area overflow-hidden d-flex">
                            <label class="checkout-lable">
                                <input type="radio" checked="checked" name="billing_address">
                                <span class="checkmark"></span>
                            </label>
                            <div class="checkout-user-details d-flex align-items-center justify-content-between w-100">
                                <div class="checkout-user-info">
                                    <h2 class="checkout-user-name">{{ $billing_addresses['name'] }}</h2>
                                    <p class="checkout-user-address mb-0">{{ $billing_addresses['address'] }}
                                    </p>
                                </div>
                                <div class="btn-groups">
                                    <a href="javascript:;" class="edit-user btn-secondary orderAddress" data-type="billing" data-id="{{ $billing_addresses['id'] }}" >Edit</a>
                                </div>
                            </div>
                        </div>
                       @endif

                        
						</div>
						
						
						
						
						
                        @if(Route::currentRouteName() == 'checkout')
                        <div class="shipping-address-area billing-area">
                          
                            <div class="minicart-btn-area d-flex align-items-center justify-content-between flex-wrap">
                                <a href="{{ route('cart') }}" class="checkout-page-btn minicart-btn btn-primary">CART</a>
                                <!-- <a href="cart.php" class="checkout-page-btn minicart-btn btn-secondary">BACK TO
                                    CART</a> -->
                                <a href="{{ route('home') }}" class="checkout-page-btn minicart-btn btn-primary">CONTINUE SHOPPING</a>
                            </div>
                        </div>
                        @endif