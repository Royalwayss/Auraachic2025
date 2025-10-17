@extends('front.layout.layout')
@section('content')
<!-- breadcrumb start -->
<div class="breadcrumb">
    <div class="container p-0">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><i class="fa-solid fa-angle-right"></i></li>
			<?php echo $breadcrumbs; ?>
        </ul>
    </div>
</div>
<!-- breadcrumb end -->
<section class="section block-banner-listing">
    <div class="container-fluid">
        <div class="product-banner">
            <h3 class="heading-title-medium color-white mb-50 wow fadeInRight"
                style="visibility: visible; animation-name: fadeInRight;">Discover Style. Define You</h3>
        </div>
    </div>
</section>
<main id="MainContent" class="content-for-layout">
   <div class="collection">
      <div class="container-fluid listing-prdt">
         <div class="row flex-row-reverse">
            <div class="col-lg-10 col-md-12 col-12">
               <div class="filter-sort-wrapper d-flex justify-content-between flex-wrap">
                  <div class="collection-title-wrap d-flex align-items-end">
                     <h2 class="collection-title heading_24 mb-0">All Products</h2>
                     <p class="collection-counter text_16 mb-0 ms-2" id="no_of_products">({{ $total_products }} @if($total_products > 1)items @else item @endif)</p>
                  </div>
                 @if(!isset($_GET['keyword'])) 
				 <div class="filter-sorting">
                     <div class="sorting position-relative d-lg-block">
                        @php  $sort_array = ['new-arrival'=>'New Arrival','featured'=>'Featured products','lth'=>'Price, low to high','htl'=>'Price, high to low']; @endphp
                        <select name="sort" class="form-select getsort " aria-label="Default select example">
                           <option value="" selected>Sort By</option>
                           @foreach($sort_array as $sort_key=>$sort_value)
                           <option value="{{ $sort_key }}" @if(isset($_GET['sort']) && $_GET['sort'] ==$sort_key) selected  @endif>{{ $sort_value }}</option>
                           @endforeach
                        </select>
                     </div>
                     <div class="border p-1 filter-drawer-trigger mobile-filter d-flex align-items-center mt-3 d-lg-none">
                        <span class="mobile-filter-icon me-2"><i class="fa-solid fa-arrow-up-wide-short"></i> </span>
                        <span class="mobile-filter-heading">Filter</span>
                     </div>
                  </div>
				  @endif
               </div>
               <div id="appendProductListing">
                  @include('front.pages.products.listing.include.product-list')
               </div>
            </div>
			@if(!isset($_GET['keyword'])) 
               @include('front.pages.products.listing.include.filter')
		    @endif
         </div>
      </div>
   </div>
</main>
@endsection