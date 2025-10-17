@extends('front.layout.layout')
@section('content')

<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="{{ route('home') }}">Home</a></li>
			<li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
            <li><a href="{{ route('account',['profile']) }}">Account</a></li>
			<li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
            <li><a href="{{ route('account',['orders']) }}">Orders</a></li>
			
			<li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
			<li>Orders Details #{{ $orderDetails['id'] }}</li>
        </ul>
    </div>
</div>


<main id="MainContent" class="content-for-layout">
    <section class="collection listing">
        <div class="accWrap container">
            <div class="row">
                 @include('front.pages.account.account_tabs')
                <div class="col-lg-9 col-md-12 address-detail-area">
                   <!-- <div class="btn-groups">
					   <a class="edit-user btn-secondary" href="{{ route('account',['order']) }}">Back</a>
                    </div>-->
					<h3>Order History</h3>
                    <hr>
					
					<div class="accPage">
                        <div class="order-Time-id">
                            <span>Ordered on {{ date("d F Y h:ia", strtotime($orderDetails['created_at'])); }}</span>
                            <span><strong>|</strong></span>
                            <span>Order Id #{{ $orderDetails['id'] }}</span>
                        </div> 
                        <div class="orderInfo">
                            <div class="row">
                                <div class="col-12 col-md-6 col-lg-3 order-details-Info">
                                    <h5>Delivery Address</h5>
                                    <p>{{ $orderDetails['order_address']['shipping_name'] }}</p>
                                    <p>Address: 
									{{ $orderDetails['order_address']['shipping_address'] }}
									@if($orderDetails['order_address']['shipping_address_line2'] != '')
										,{{ $orderDetails['order_address']['shipping_address_line2'] }}
									@endif
									</p>
                                    <p>City: {{ $orderDetails['order_address']['shipping_city'] }}</p>
                                    <p>State: {{ $orderDetails['order_address']['shipping_state'] }}</p>
                                    <p>Mobile: {{ $orderDetails['order_address']['shipping_mobile'] }}</p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3 order-details-Info">
                                    <h5>Payment Method</h5>
                                    <p>{{ $orderDetails['payment_method'] }}</p>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3 order-details-Info">
                                    <h5>Order Info</h5>
                                    <div class="orderFlexWrap">
                                        <div class="orderWrap">
                                            <label>Order Quantity:</label>
                                            <span>{{ $orderDetails['total_items'] }}</span>
                                        </div>
                                        <div class="orderWrap">
                                            <label>Order Status:</label>
                                            <span>{{ $orderDetails['order_status'] }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6 col-lg-3 order-details-Info">
                                    <h5>Order Info</h5>
                                    <div class="orderFlexWrapEnd">
                                        <div class="orderWrap">
                                            <label>Sub Total:</label>
                                            <span>₹ {{ $orderDetails['sub_total'] }}</span>
                                        </div>
                                        @if(!empty($orderDetails['shipping_charges']))
										<div class="orderWrap">
                                            <label>Shipping Charges:</label>
                                            <span>₹ {{ $orderDetails['shipping_charges'] }}</span>
                                        </div>
										@endif
                                       
                                        <div class="orderWrap">
                                            <label>Coupon Discount:</label>
                                            <span>₹ 0</span>
                                        </div>
                                        @if(!empty($orderDetails['coupon_discount'])) 
										<div class="orderWrap">
                                            <label>Grand Total:</label>
                                            <span>₹ {{ $orderDetails['coupon_discount'] }}</span>
                                        </div>
										@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="orderProd">
                            @foreach($orderDetails['order_products'] as $order_product) 
							<div class="row"> 
                                <div class="col-12 col-md-2">
                                    <div class="prodImg">
                                        <a target="_blank" href="{{ route('product',[$order_product['productdetail']['product_url'],$order_product['productdetail']['id']]) }}">
                                            @if(!empty($order_product['productdetail']['product_image']))
											<img src="{{ asset('front/images/products/small/'.$order_product['productdetail']['product_image']['image']) }}">
											@endif
                                        </a>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="ordProdInfo hist">
                                        <a href="{{ route('product',[$order_product['productdetail']['product_url'],$order_product['productdetail']['id']]) }}">{{ $order_product['product_name'] }} </a>
                                        <div class="orderWrap">
                                            <label>SKU:</label>
                                            <span>{{ $order_product['product_sku'] }} </span>
                                        </div>
                                        <div class="orderWrap">
                                            <label>Price:</label>
                                            <span>₹ {{ $order_product['final_price'] }}</span>
                                        </div>
                                        <div class="orderWrap">
                                            <label>Quantity:</label>
                                            <span>{{ $order_product['product_qty'] }}</span>
                                        </div>
                                        <div class="orderWrap">
                                            <label>Size:</label>
                                            <span>{{ $order_product['product_size'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
						</div>
                    </div>
                </div>

            </div>
        </div>
    </section>
</main>



@endsection