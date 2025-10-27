@extends('front.layout.layout')
@section('content')
<div class="breadcrumb">
   <div class="container">
      <ul class="list-unstyled d-flex align-items-center m-0">
         <li><a href="{{ route('home') }}">Home</a></li>
         <li>
            <i class="fa-solid fa-angle-right"></i>
         </li>
         <li>Order Cancel</li>
      </ul>
   </div>
</div>
<main id="MainContent" class="cartpage content-for-layout">
   <div class="about-page">
      <div class="container privacy-content mt-5 mb-5" >
         <div class="row" style="text-align:center;justify-content: center;">
            <!-- <i class="fa-solid fa-check"></i> -->
            <img style="width:100px" src="{{ asset('front/images/cross.png') }}" class="gif-img">
            <h2>YOUR ORDER HAS BEEN CANCELLED!</h2>
            <p>Payment has not been made and your order has been cancelled.</p>
            <center><a href="{{url('/')}}"><button class="position-relative review-submit-btn contact-submit-btn" type="button" name="subscribe">Continue Shopping</button></a></center>
         </div>
      </div>
   </div>
</main>
@endsection