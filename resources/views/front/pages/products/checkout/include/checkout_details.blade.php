 <div class="col-xl-4 col-lg-4 col-md-12 col-12 sumTab Checkout-box">
                        <h4 class="cart-total-title mt-3 mb-3">Order Summary</h4>
                        <div class="card">
                            <div class="card-body">
                                <span id="grandTotalCOD" data-value="669"></span><span id="grandTotalPrepaid"
                                    data-value="639.05"></span>
                                <table class="table table-borderless">
                                    <thead>
                                        <tr>
                                            <th style="width:70%">Product</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @foreach($cartitems['items'] as $cartitem)
								         @php $priceDetails = $cartitem['priceDetails'];  @endphp 
										<tr>
                                            <td><a class="black" href="{{ route('product',[$cartitem['product']['product_url'],$cartitem['product']['id']]) }}">{{ $cartitem['product']['product_name'] }}</a> x  {{ $cartitem['qty'] }}
                                                <br>Size: {{ $cartitem['size'] }}
												@if(!empty($cartitem['product']['product_gst']))
                                                <br>Included GST: {{ $cartitem['product']['product_gst'] }}%
												@endif
                                            </td>
                                            <td class="text-right">₹ {{ amount_format($priceDetails['subtotal']) }}</td>
                                        </tr>
										@endforeach
										@php $summery = $cartitems['cartPricing']; @endphp
                                        <tr>
                                            <td>Sub Total</td>
                                            <td class="text-right">₹ {{ amount_format($summery['subtotal']) }}</td>
                                        </tr>
                                        @if(!empty($summery['discount']))
										<tr>
                                            <td>Coupon Discount (If Any) (-)</td>
                                            <td class="text-right">₹ {{ amount_format($summery['discount']) }}</td>
                                        </tr>
										@endif
                                        <?php /*
										<tr id="prepaidDiscount">
                                            <td>Prepaid Discount (If Any) (-)</td>
                                            <td>₹ <span id="prepaidDiscountValue" data-value="29.95">29.95</span></td>
                                        </tr> 
                                        <tr>
                                            <td>Credit (If Any) (-)</td>
                                            <td>₹ 0</td>
                                        </tr> */ ?>
                                      
										<tr>
                                            <td class="shipping-charges">Shipping Charges (+) @if(empty($summery['shipping'])) <strong>Free </strong> @endif</td>
                                            <td class="text-right"> ₹ {{ amount_format($summery['shipping']) }} </td>
                                        </tr>
                                       
										<!-- <tr>
                                                    <td>Taxes (Inclusive)</td>
                                                    <td>₹ <span id="gstAmount">29.95</span></td>
                                                </tr> -->
                                        <tr>
                                            <td>Grand Total (Inclusive of all Taxes)</td>
                                            <td class="text-right"><span id="grandTotal"><strong>₹ {{ amount_format($summery['grandtotal']) }}</strong></span></td>
                                        </tr>
                                        <!-- Hidden Values for jQuery -->


                                    </tbody>
                                </table>

                                <form id="OrderPlace" action="{{ route('placeOrder') }}" autocomplete="off" method="post">@csrf  
                                    <h5 class="cart-total-title mt-4 mb-3">Choose Payment Method</h5>

                                   @foreach($paymentMethods as $paymentMethod) 
                                    <div class="form__radio-group">
                                        <input id="{{ $paymentMethod['payment_method'] }}" type="radio" class="form__radio-input" name="paymentMode"
                                            value="{{ $paymentMethod['payment_method'] }}" >
                                        <label for="{{ $paymentMethod['payment_method'] }}" class="form__radio-label">
                                            <span class="form__radio-button"></span>
                                            <span class="form__radio-label-text">{{ $paymentMethod['label'] }}</span>
                                        </label>
                                    </div>
							     @endforeach
                                    


                                    <div class="agree-term">
                                        <input class="" type="checkbox" name="agree" id="agree2" value="1"
                                            >
                                        <label class="term-label" for="agree2">
                                            I Agree to <a target="_blank" href="{{ url('term-and-conditions') }}">Terms &amp; Conditions</a>
                                        </label>
                                    </div>

                                    <button id="PlaceOrder" type="submit" class="icButton w-100 mt-3">Place
                                        Order</button>
                                </form>

                            </div>
                        </div>
                    </div>
                