@extends('front.layout.layout')
@section('content')

<main id="MainContent" class="content-for-layout">
   <!-- ====== start=============================================== -->
   <div class="thank-you mt-150 mb-80 text-center classforHeader">
       <div class="container-wrapper pl-15 pr-15 mt-5">
            <div class="row text-center mb-5">
                <img src="{{ asset('front/assets/img/thankyou.gif') }}" alt="thankyou" class="img-fluid">
                <h2 class="text-center text-black">Thank You</h2>
                <div class="order-no"><a href="{{ route('account',['orders']) }}?id={{ $orderdetails['id'] }}">Order ID: {{ $orderdetails['id'] }}</a></div>
                <div >Grand Total: <strong>₹ {{ amount_format($orderdetails['grand_total']) }}</strong></div>
                @if($orderdetails['payment_method'] == 'COD')
                   <p>"Thank you for your order! Your order was placed successfully and we’re now preparing your items for shipment."</p>
                @else
                 <p>"Thank you for your order! Your transaction was successful and we’re now preparing your items for shipment."</p>
                @endif
                <a href="{{ route('home') }}">Continue Shopping <i class="fas fa-shopping-bag"></i></a>
            </div>
        </div><!-- /container -->
    </div>

   <!-- end -->
</main>
 <?php
      Session::forget('couponinfo');
      Session::forget('orderid');
      ?>
@endsection