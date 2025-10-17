<?php
   use App\Models\Product; 
   use App\Models\ProductsAttribute; 
   use App\Models\Wishlist;
   ?>
@extends('front.layout.layout')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb">
   <div class="container">
      <ul class="list-unstyled d-flex align-items-center m-0">
         <li><a href="{{ route('home') }}">Home</a></li>
         <li>
            <i class="fa-solid fa-angle-right"></i>
         </li>
         <li><a href="{{ url($productdetails['category']['url']) }}">{{ $productdetails['category']['category_name'] }}</a></li>
         <li>
            <i class="fa-solid fa-angle-right"></i>
         </li>
         <li>{{ $productdetails['product_name'] }}</li>
      </ul>
   </div>
</div>
<!-- breadcrumb end -->
<main id="MainContent" class="content-for-layout">
   <div class="product-page">
   <div class="container">
      <div class="detailRight sticky-section">
         <div class="row">
            @include('front.pages.products.details.include.product_images') 
            <div class="col-lg-6 col-md-12 col-12 mt-2">
               <div class="product-details ps-lg-4">
                  <h2 class="product-title mb-3">{{$productdetails['product_name']}}</h2>
                  <div class="product-price-wrapper mb-2" id="product_price_details">
                     MRP
                     <span class="product-price regular-price product_final_price">₹{{round($productdetails['final_price'],2)}}</span>
                     @if($productdetails['product_discount']>0)
                     <del class="product-price compare-price">₹{{round($productdetails['product_price'],2)}}</del>
                     @endif
                     @if($productdetails['product_discount']>0)
                     <span class="text-include">{{ $productdetails['product_discount'] }}% off (Incl. of all taxes)</span>
                     @endif
                  </div>
                  @if(!empty($avgStarRating))
				  <div class="product-rating d-flex align-items-center mb-3">
                     <span class="star-rating">
                     @for($i=0; $i<$avgStarRating; $i++)
					  <i class="fa-solid fa-star"></i>
					 @endfor
					 
                     </span>
                     <span class="rating-count ms-2">({{ $ratingCount }})</span>
                  </div>
				  @endif
                  <div class="product-sku product-meta mb-1">
                     <strong class="label">Product Code:</strong> {{$productdetails['product_code']}}
                  </div>
                  <hr>
                  <div class="product-variant-select">
                     @if(!empty($productdetails['groups']))
                     <div class="product-variant-selects">
                        <strong class="label mb-1 d-block">Color:</strong>
                        <ul class="variant-list list-unstyled d-flex align-items-center flex-wrap">
                           @foreach($productdetails['groups'] as $group)
                           <li class="variant-item">
                              <label class="variant-label">
                              <a href="{{ route('product',[$group['product_url'],$group['id']]) }}">
                              @if(!empty($group['product_image']))
                              <img src="{{ asset('front/images/products/small/'.$group['product_image']['image']) }}"/>
                              @endif
                              </a>
                              </label>
                           </li>
                           @endforeach
                        </ul>
                     </div>
                     <hr>
                     @endif
                     <div class="product-variant product-variant-other">
                        @if($productdetails['category']['size_chart'] != '')
                        <div class="d-flex align-items-center justify-content-between"><strong class="label mb-1 d-block " >Select Size:</strong>
                           <a href="#size-modal" class="size-chart" data-bs-toggle="modal" tabindex="0">
                           <i class="fa-solid fa-ruler-combined"></i> Size Chart
                           </a>
                        </div>
                        @endif
						@php $total_stock =0; @endphp
                        <ul class="variant-list list-unstyled d-flex align-items-center flex-wrap">
                           @if(count($productdetails['attributes'])>0) 
                           @foreach ($productdetails['attributes'] as $key => $attribute)
					       @php $total_stock +=$attribute['stock']; @endphp
                           <li data-type="product" class="variant-item @if(!empty($attribute['stock'])) product_size @endif" data-size="{{ $attribute['size'] }}" data-product_id="{{ $productdetails['id'] }}" data-key="{{ $key }}">
                              <input type="radio" class="chkPrice" name="product_size" id="product-size-{{ $key }}"   value="{{ $attribute['size'] }}" @if(empty($attribute['stock'])) disabled @endif  >
                              <label class="variant-label">{{ $attribute['size'] }}</label>
                           </li>
                           @endforeach
                           @endif
                        </ul>
                     </div>
                  </div>
                  <form class="product-form" action="javascript:;" id="AddtoCart">
                     @csrf 
                     <input type="hidden" name="size" id="product_size">
                     <input type="hidden" name="product_id" id="product_id" value="{{ $productdetails['id'] }}">
                     <div class="misc d-flex align-items-center justify-content-between product_quantity mt-4">
                        <div class="quantity d-flex align-items-center justify-content-between">
                           <button class="qty-btn dec-qty"><img src="{{ asset('front/assets/img/icon/minus.svg') }}"
                              alt="minus"></button>
                           <input class="qty-input" type="number" name="qty" value="1" min="0">
                           <button class="qty-btn inc-qty"><img src="{{ asset('front/assets/img/icon/plus.svg') }}" alt="plus"></button>
                        </div>
                       
						   <div>
						       @if(!empty($total_stock)) 
					           <span class="product-availability d-flex align-item-center">In Stock</span>
						       @else
					           <span class="product-availability d-flex align-item-center">Out of Stock</span>
							   @endif
					       </div> 
					  
                     </div>
                     <hr>
                     <div class="availability">
                        <input id="txtZipCode" name="txtZipCode" type="number" placeholder="Enter Pincode" maxlength="6">
                        <button class="icButton checkpincode mx-0" type="submit" onclick="javascript:;">Check</button>
                     </div>
                     <div class="product-form-buttons d-flex align-items-center justify-content-between mt-4">
                        <button data-cart-type="page" type="submit" class="position-relative btn-atc btn-add-to-cart loader add-to-cart" data-type="cart">ADD TO CART</button>
                        <button data-cart-type="page" type="submit" class="position-relative btn-atc btn-buyit-now add-to-cart" data-type="buy">BUY IT NOW</button>
                        <a data-cart-type="page" href="javascript:;"  data-product-id="{{ $productdetails['id'] }}" class="product-wishlist product-wishlist-btn"><i class="fa-regular fa-heart"></i> </a>
                     </div>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
   @include('front.pages.products.details.include.product_details')
   @include('front.pages.products.details.include.related_products')
</main>
@if($productdetails['category']['size_chart'] != '')
@php
$isImage = preg_match('/\.(webp|jpg|jpeg|png|gif)$/i', $productdetails['category']['size_chart']);
@endphp
<div class="modal fade" tabindex="-1" id="size-modal">
   <div class="modal-dialog modal-xl modal-dialog-centered modal-l">
      <div class="modal-content">
         <div class="modal-header border-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body pb-5">
            @if($isImage)
            <img  src="{{ asset('front/images/sizecharts/'.$productdetails['category']['size_chart']) }}" />
            @else
            <?php echo $productdetails['category']['size_chart']; ?>
            @endif
         </div>
      </div>
   </div>
</div>
@endif

@endsection