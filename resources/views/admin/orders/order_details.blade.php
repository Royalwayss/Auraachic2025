<?php use App\Models\Product; ?>
@extends('admin.layout.layout')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          @if(Session::has('success_message'))
            <div class="col-sm-12">
              <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin-top: 10px;">
                  {{ Session::get('success_message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
            {{ Session::forget('success_message') }}
          @endif
          @if(Session::has('error_message'))
            <div class="col-sm-12">
              <div class="alert alert-warning alert-dismissible fade show" role="alert" style="margin-top: 10px;">
                  {{ Session::get('error_message') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
            </div>
            {{ Session::forget('error_message') }}
          @endif
          
          <div class="col-sm-3">
            <h1>Orders Management</h1>
          </div>
          <div class="col-sm-9 d-flex justify-content-between align-items-center">
            <div class="d-flex gap-1">
        @if($prevId != 0)
            <a href="{{ url('admin/orders/'.$prevId) }}" class="btn btn-primary btn-animated-link" style="margin-left:-45px; font-size: 14px; padding: 5px 10px;">
                <i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Previous Order
            </a>
        @endif
        @if($nextId != 0)
            <a href="{{ url('admin/orders/'.$nextId) }}" class="btn btn-primary btn-animated-link" style="margin-left:5px; font-size: 14px; padding: 5px 10px;">
                Next Order <i class="fas fa-arrow-right"></i>
            </a>
        @endif
    </div>
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
        <li class="breadcrumb-item active">Order #{{ $orderDetails['id'] }} Detail</li>
    </ol>

    
</div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Order Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                      <td>Order Date</td>
                      <td>{{  date('d-m-Y', strtotime($orderDetails['created_at'])) }}</td>
                    </tr>
                    <tr>
                      <td>Order Status</td>
                      <td>{{ $orderDetails['order_status'] }}</td>
                    </tr>
                    @if(!empty($orderDetails['delivery_method']))
                    <tr>
                      <td>Courier Name</td>
                      <td>{{ $orderDetails['delivery_method'] }}</td>
                    </tr>
                    @endif
                    @if(!empty($orderDetails['awb_number']))
                    <tr>
                      <td>Tracking (Reference) Number</td>
                      <td>{{ $orderDetails['awb_number'] }}</td>
                    </tr>
                    @endif
                    <tr>
                      <td>Order Total</td>
                      <td>₹{{ round($orderDetails['grand_total'],2) }}</td>
                    </tr>
                    <tr>
                      <td>Shipping Charges (+)</td>
                      <td>₹{{ round($orderDetails['shipping_charges'],2) }}</td>
                    </tr>
                    <tr>
                      <td>Taxes (Inclusive)</td>
                      <td>₹{{ round($orderDetails['taxes'],2) }}</td>
                    </tr>
                    <tr>
                      <td>Coupon Code </td>
                      <td>{{ $orderDetails['coupon_code'] }}</td>
                    </tr>
                    <tr>
                      <td>Coupon Amount (-)</td>
                      <td>₹{{ round($orderDetails['coupon_discount'],2) }}</td>
                    </tr>
                    <tr>
                      <td>Credit Amount (-)</td>
                      <td>₹{{ round($orderDetails['credit'],2) }}</td>
                    </tr>
                    <tr>
                      <td>Prepaid Discount (-)</td>
                      <td>₹{{ round($orderDetails['prepaid_discount'],2) }}</td>
                    </tr>
                    <tr>
                      <td>Payment Method</td>
                      <td>{{ ucwords($orderDetails['payment_method']) }}</td>
                    </tr>
                    <tr>
                      <td>Payment Gateway</td>
                      <td>{{ ucwords($orderDetails['payment_gateway']) }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Delivery Address</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                    <td>Name</td>
                    <td>{{$orderDetails['order_address']['shipping_name']}}</td>
                  </tr>
                  @if(isset($orderDetails['order_address']['company']))
                  <tr>
                    <td>Company</td>
                    <td>{{$orderDetails['order_address']['company']}}</td>
                  </tr>
                  @endif
                  <tr>
                    <td>Address</td>
                    <td>{{$orderDetails['order_address']['shipping_address']}}</td>
                  </tr>
                  @if(isset($orderDetails['order_address']['apartment']))
                  <tr>
                    <td>Apartment,suite,etc.</td>
                    <td>{{$orderDetails['order_address']['apartment']}}</td>
                  </tr>
                  @endif
                  <tr>
                    <td>City</td>
                    <td>{{$orderDetails['order_address']['shipping_city']}}</td>
                  </tr>
                  <tr>
                    <td>State</td>
                    <td>{{$orderDetails['order_address']['shipping_state']}}</td>
                  </tr>
                  <tr>
                    <td>Country</td>
                    <td>{{$orderDetails['order_address']['shipping_country']}}</td>
                  </tr>
                  <tr>
                    <td>Pincode</td>
                    <td>{{$orderDetails['order_address']['shipping_postcode']}}</td>
                  </tr>
                  <tr>
                    <td>Mobile</td>
                    <td>{{$orderDetails['order_address']['shipping_mobile']}}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Customer Details</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                    <td>Name</td>
                    <td>{{$orderDetails['getuser']['name']}}</td>
                  </tr>
                  <tr>
                    <td>Email</td>
                    <td>{{$orderDetails['getuser']['email']}}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Billing Address</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                    <td>Name</td>
                    <td>{{$orderDetails['order_address']['billing_name']}}</td>
                  </tr>
                  <tr>
                    <td>Address</td>
                    <td>{{$orderDetails['order_address']['billing_address']}}</td>
                  </tr>
                  <tr>
                    <td>City</td>
                    <td>{{$orderDetails['order_address']['billing_city']}}</td>
                  </tr>
                  <tr>
                    <td>State</td>
                    <td>{{$orderDetails['order_address']['billing_state']}}</td>
                  </tr>
                  <tr>
                    <td>Country</td>
                    <td>@if(empty($orderDetails['order_address']['billing_country'])) India @else{{$orderDetails['order_address']['billing_country']}} @endif</td>
                  </tr>
                  <tr>
                    <td>Pincode</td>
                    <td>{{$orderDetails['order_address']['billing_postcode']}}</td>
                  </tr>
                  <tr>
                    <td>Mobile</td>
                    <td>{{$orderDetails['order_address']['billing_mobile']}}</td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Update Order Status</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table class="table table-bordered">
                  <tbody>
                    <tr>
                    <td colspan=2>
                      <form action="{{ url('admin/update-order-status') }}" method="post">@csrf
                      <input type="hidden" name="order_id" value="{{ $orderDetails['id'] }}">
                      <select name="order_status" id="order_status" required="">
                        <option>Select</option>
                        @foreach($getorderstatus as $status)
                          <option value="{{ $status['name'] }}" @if(isset($orderDetails['order_status']) && $orderDetails['order_status']==$status['name']) selected="" @endif>{{ $status['name'] }}</option>
                        @endforeach
                      </select>&nbsp;&nbsp;
                      <input style="width: 110px; display: none;" type="text" name="delivery_method" @if(empty($orderDetails['delivery_method'])) id="delivery_method" @endif placeholder="Courier Name" value="{{ $orderDetails['delivery_method'] }}">
                      <input style="width: 110px; display: none;" type="text" name="awb_number" @if(empty($orderDetails['awb_number'])) id="awb_number" @endif placeholder="Tracking (Reference) No." value="{{ $orderDetails['awb_number'] }}">
                      <button type="submit">Update</button>
                    </form>
                    </td>
                  </tr>
                  <tr>
                    <td colspan="2">
                      @foreach($orderDetails['histories'] as $history)
                        <strong>{{ $history['order_status'] }}</strong>
                          @if(isset($orderDetails['delivery_method']) && !empty($orderDetails['delivery_method']) && $history['order_status']=="Shipped")
                          	<br>Shipped by <strong>{{ $orderDetails['delivery_method']}}</strong>
                          @endif
                          @if(isset($orderDetails['awb_number']) && !empty($orderDetails['awb_number']) && $history['order_status']=="Shipped")
                          	<br>Tracking (Reference) Number: <strong>{{ $orderDetails['awb_number']}}</strong>
                          @endif
                          @if(isset($orderDetails['is_pushed']) && $orderDetails['is_pushed']==1)
                            <span style="Color:green">(Order Pushed to ShipRocket)</span>
                          @endif
                          <br>
                        {{  date('j F, Y, g:i a', strtotime($history['created_at'])) }}
                        <hr>
                      @endforeach
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>

          </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Ordered Products</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <thead>
                    <tr>
                        <th>
                            Image
                        </th>
                        <th>
                            Name
                        </th>
                        <th>
                            Size
                        </th>
                        <th>
                            MRP
                        </th>
                        <th>
                            Discount
                        </th>
                        <th>
                            Price
                        </th>
                        <th>
                            Quantity
                        </th>
                        <th>
                            Sub Total
                        </th>
                        <th>
                            Item Status
                        </th>
                    </tr>
                  </thead>
                  <tbody>
					<?php $priceArr = array(); ?>
                    @foreach($orderDetails['order_products'] as $key => $product)
                    <?php $priceArr[] = $product['sub_total'] ?>
                    <tr>
                        <td>
                            @if(isset($product['productdetail']['product_image']))
                                <img width="100px" src="{{asset('front/images/products/small/'.$product['productdetail']['product_image']['image'])}}">
                            @endif
                        </td>
                        <td>
                        	<?php $getProductURL = Product::productURL($product['product_name']); ?>
                            <a  target="_blank" href="{{ url('product/'.$getProductURL.'/'.$product['product_id']) }}">{{$product['product_name']}}</a>
                        </td>
                        <td>
                            {{$product['product_size']}}
                        </td>
                        <td>
                            ₹{{round($product['mrp'],2)}}
                        </td>
                        <td>
                            ₹{{$product['product_discount_amount']}}
                        </td>
                        <td>
                            ₹{{round($product['product_price'],2)}}
                        </td>
                        <td>
                            {{$product['product_qty']}}
                        </td>
                        <td>
                            ₹{{round($product['sub_total'],2)}}
                        </td>
                        <td>
                            {{$product['item_status']}}
                        </td>
                    </tr>
        			@endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection