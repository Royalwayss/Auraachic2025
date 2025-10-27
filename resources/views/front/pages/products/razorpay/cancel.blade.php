@extends('front.layout.layout')
@section('content')
<section class="collection listing exist thankYou">
    <div class="innercontainer">
        <!-- <i class="fa-solid fa-check"></i> -->
        <img src="{{ asset('front/images/cross.png') }}" class="gif-img">
        <h2>YOUR ORDER HAS BEEN CANCELLED!</h2>
        <p>Payment has not been made and your order has been cancelled.</p>
        <a href="{{url('/')}}"><button class="suscribeBtn" type="button" name="subscribe">Continue Shopping</button></a>
        <div class="shopping-bag"><img src="{{ asset('front/images/bag.png') }}" class="img-fluid"></div>
    </div>
</section>
@endsection