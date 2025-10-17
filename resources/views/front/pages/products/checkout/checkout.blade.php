@extends('front.layout.layout')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="/">Home</a></li>
            <li>
                <i class="fa-solid fa-angle-right"></i>
            </li>
            <li>Cart</li>
            <li>
                <i class="fa-solid fa-angle-right"></i>
            </li>
            <li>Checkout</li>
        </ul>
    </div>
</div>
<!-- breadcrumb end -->

<main id="MainContent" class="content-for-layout">
 
	 @if(Session::has('flash_message_error'))
		 <div class="container">  
          <div class="row">
              <div class="col-12">	 
			 <div class="alert alert-warning alert-dismissible fade show" role="alert">
			  <strong>Error!</strong> {!! session('flash_message_error') !!}
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			</div>
		</div>
		</div>
	@endif
	
	<div class="checkout-page cart-page">
        <div class="container billing-pages">
            <div class="section-header mb-3">
                <h2 class="section-heading">Checkout</h2>
            </div>
			
            <div class="checkout-page-wrapper">
                <div class="row">
                   <div class="col-xl-8 col-lg-8 col-md-12 col-12" id="order_address">
				       @include('front.pages.products.checkout.include.order_address')
				   </div>
                   @include('front.pages.products.checkout.include.checkout_details')
				</div>
            </div>
        </div>
    </div>
</main>
 <!-- Modal -->
@endsection