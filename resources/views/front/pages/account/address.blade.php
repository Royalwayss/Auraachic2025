@extends('front.layout.layout')
@section('content')
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
            <li><a href="{{ route('account',['profile']) }}">Account</a></li>
			<li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
			<li>Address</li>
        </ul>
    </div>
</div>

<main id="MainContent" class="content-for-layout">
    <section class="collection listing">
        <div class="accWrap container">
            <div class="row">
                @include('front.pages.account.account_tabs')
                <div class="col-lg-9 col-md-12 address-detail-area">
                    <h3>Address</h3>
                    <hr>
                    <div class="accPage">
                      <div class="row" id="order_address"> 
					      @include('front.pages.products.checkout.include.order_address')
                      </div>
					</div>
                </div>

            </div>
        </div>
        <!-- Edit Address Modal -->

       
    </section>
</main>
@endsection