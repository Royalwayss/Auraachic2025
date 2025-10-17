@extends('front.layout.layout')
@section('content')
<style>
.couponCard {
    background-color: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    margin-bottom: 10px;
}
.couponHeader {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}
.couponSection {
    margin: 0 0 20px;
}
.couponSection {
    padding: 20px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.coupon.field_form.input-group {
    gap: 0;
    margin-bottom: var(--spacingMd);
}
input-group {
    position: relative;
    display: flex
;
    flex-wrap: wrap;
    align-items: stretch;
    width: 100%;
}
.couponTitle {
    font-size: 16px;
    color: #333;
}
.copyBtn {
    background-color: transparent;
    border: none;
    cursor: pointer;
    color: #3b386c;
    font-size: 18px;
	border-radius: 13px;
}
.copyBtn .fa-check{ color:white; }
.couponDetails {
    list-style-type: none;
    padding: 0;
    margin: 0;
    font-size: 14px;
    color: #555;
}
.copyMsg {
    font-size: 12px;
    color: green;
    display: none;
    position: absolute;
    bottom: 25px;
    right: 15px;
}
</style>
<!-- breadcrumb start -->
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li>
                <i class="fa-solid fa-angle-right"></i>
            </li>
            <li>Cart</li>
        </ul>
    </div>
</div> 
<!-- breadcrumb end -->
<main id="MainContent" class="cartpage content-for-layout">
    <div class="cart-page">
        <div class="container billing-pages">
             <div class="section-header mb-3">
                <h2 class="section-heading">Your Cart</h2>
            </div>
            <div class="cart-page-wrapper">
                <div class="row cart_details" id="appendCartItems">
                    @include('front.pages.products.cart.include.cart_details')
				</div>
            </div>
        </div>
    </div>
</main>
@endsection