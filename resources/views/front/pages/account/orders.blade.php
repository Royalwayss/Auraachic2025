@extends('front.layout.layout')
@section('content')
<?php 
Use App\Models\Order;
?>
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="{{ route('home') }}">Home</a></li>
            <li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
            <li><a href="{{ route('account',['profile']) }}">Account</a></li>
			<li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
			<li>Orders</li>
        </ul>
    </div>
</div>

<main id="MainContent" class="content-for-layout">
    <section class="collection listing">
       
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
	
	 @if(Session::has('flash_message_success'))
		 <div class="container">  
          <div class="row">
              <div class="col-12">	 
			 <div class="alert alert-success alert-dismissible fade show" role="alert">
			  <strong>Success!</strong> {!! session('flash_message_success') !!}
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
				<span aria-hidden="true">&times;</span>
			  </button>
			</div>
			</div>
		</div>
		</div>
	@endif
	
	   <div class="accWrap container">
            <div class="row">
                 @include('front.pages.account.account_tabs')
                <div class="col-lg-9 col-md-12 address-detail-area">
                    <h3>Order History</h3>
                    <hr>
                    <div class="accPage table-responsive">
                        <table class="order-table table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Order Date</th>
                                    <th>Status</th>
                                    <th>Total</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($orders as $order)
								<tr>
                                    <td>#{{ $order['id'] }}</td>
                                    <td>{{ date("d F Y h:ia", strtotime($order['created_at'])) }}</td>
                                    <td>{{ $order['order_status'] }}</td>
                                    <td>₹ {{ $order['grand_total'] }}</td>
                                    <td>
                                        <a href="{{ route('account',['orders']) }}?id={{ $order['id'] }}" class="view-button"><i class="fas fa-eye"></i></a>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<a target="_blank"  href="{{ route('orderinvoice',[$order['id']]) }}" class="view-button"><i class="fas fa-print"></i></a>
                                        @if(Order::check_order_cancel($order['id']) == 1)
										&nbsp;&nbsp;&nbsp;&nbsp;<a title="Cancel Order"  onclick="return confirm('Are you sure you want to cancel order?');" href="{{ route('cancelOrder',[$order['id']]) }}" class="cancel-button"><i class="fa-solid fa-trash"></i></a>
                                        @endif
									</td>
                                </tr>
								@endforeach
							</tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
        <!-- Edit Address Modal -->
    </section>
</main>

@endsection