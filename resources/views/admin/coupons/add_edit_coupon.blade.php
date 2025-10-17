@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Coupons Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
              <li class="breadcrumb-item active">{{ $title }}</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">

        <div class="card card-default">
          <div class="card-header">
            <h3 class="card-title">{{ $title }}</h3>

            <div class="card-tools">
              <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
              </button>
              <button type="button" class="btn btn-tool" data-card-widget="remove">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <!-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif -->
                <div style="float:right;">
                @if($prevId!=0)
                  <a href="{{ url('admin/add-edit-coupon/'.$prevId) }}" class="btn btn-primary btn-animated-link"><i class="fas fa-arrow-left"></i>&nbsp;&nbsp;Previous Coupon</a>
                @endif
                @if($nextId!=0)
                  <a href="{{ url('admin/add-edit-coupon/'.$nextId) }}" class="btn btn-primary btn-animated-link"> Next Coupon  <i class="fas fa-arrow-right"></i> </a>
                @endif
                </div>
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                @endif
                @if(Session::has('success_message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Success:</strong> {{ Session::get('success_message') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
                <form name="couponForm" id="couponForm" @if(empty($coupon['id'])) action="{{ url('admin/add-edit-coupon') }}" @else action="{{ url('admin/add-edit-coupon/'.$coupon['id']) }}" @endif method="post" enctype="multipart/form-data">@csrf
                <div class="card-body">
                <div class="row">
                  @if(empty($coupon['coupon_code']))
                      <div class="form-group col-md-6">
                          <label for="coupon_option">Coupon Option</label><br>
                          <span><input id="AutomaticCoupon" type="radio" name="coupon_option" value="Automatic" checked="">&nbsp;Automatic&nbsp;&nbsp;
                          <span><input id="ManualCoupon" type="radio" name="coupon_option" value="Manual">&nbsp;Manual&nbsp;&nbsp;
                      </div>
                      <div class="form-group col-md-6" style="display: none;" id="couponField">
                          <label for="coupon_code">Coupon Code</label>
                          <input type="text" class="form-control" name="coupon_code" id="coupon_code" placeholder="Enter Coupon Code">
                      </div>
                    @else
                      <input type="hidden" name="coupon_option" value="{{ $coupon['coupon_option'] }}">
                      <input type="hidden" name="coupon_code" value="{{ $coupon['coupon_code'] }}">
                      <div class="form-group col-md-6">
                          <label for="coupon_code">Coupon Code: </label>
                          <span>{{ $coupon['coupon_code'] }}</span>
                      </div>
                    @endif
                    <div class="form-group col-md-6">
                      <label for="coupon_type">Coupon Type</label><br>
                      <span><input type="radio" name="coupon_type" value="Multiple Times" @if(isset($coupon['coupon_type'])&&$coupon['coupon_type']=="Multiple Times") checked="" @elseif(!isset($coupon['coupon_type'])) checked="" @endif>&nbsp;Multiple Times&nbsp;&nbsp;
                      <span><input type="radio" name="coupon_type" value="Single Times" @if(isset($coupon['coupon_type'])&&$coupon['coupon_type']=="Single Times") checked="" @endif>&nbsp;Single Times&nbsp;&nbsp;
                    </div>
                    <div class="form-group col-md-6">
                      <label for="amount_type">Amount Type</label><br>
                      <span><input type="radio" name="amount_type" value="Percentage" @if(isset($coupon['amount_type'])&&$coupon['amount_type']=="Percentage") checked="" @elseif(!isset($coupon['amount_type'])) checked="" @endif>&nbsp;Percentage&nbsp;(in %)&nbsp;
                      <span><input type="radio" name="amount_type" value="Fixed" @if(isset($coupon['amount_type'])&&$coupon['amount_type']=="Fixed") checked="" @endif>&nbsp;Fixed&nbsp;(in ₹)&nbsp;
                  </div>
                  <div class="form-group col-md-6">
                      <label for="amount">Amount</label>
                      <input type="number" class="form-control" name="amount" id="amount" placeholder="Enter Amount" required="" @if(isset($coupon['amount'])) value="{{ $coupon['amount'] }}" @else value="{{ old('amount') }}" @endif>
                  </div>
                  <div class="form-group col-md-6">
                      <label for="amount">Select Min Quantity</label>
                      <select name="min_qty" style="color:gray" class="form-control">
                          <option value="1">Select Min Qty</option>
                          <?php for($i=1;$i<=10;$i++) { ?>
                          <option value="{{$i}}" <?php  if(!empty($coupon['min_qty']) && $coupon['min_qty']==$i) { echo "selected"; } ?>>{{ $i }}</option>
                          <?php } ?>
                      </select>
                  </div>
                  <div class="form-group col-md-6">
                      <label for="amount">Select Max Quantity</label>
                      <select name="max_qty" style="color:gray" class="form-control">
                          <option value="">Select Max Qty</option>
                          <?php for($i=1;$i<=1000;$i++) { ?>
                          <option value="{{$i}}" <?php  if(!empty($coupon['max_qty']) && $coupon['max_qty']==$i) { echo "selected"; } elseif(empty($coupon['max_qty']) && $i==100) { echo "selected"; } ?>>{{ $i }}</option>
                          <?php } ?>
                      </select>
                  </div>
                  <div class="form-group col-md-6">
                      <label for="min_amount">Enter Min Price Range</label>
                      <input type="text" placeholder="Enter Min Amount" name="min_amount" autocomplete="off"  class="form-control" @if(isset($coupon['min_amount'])) value="{{ $coupon['min_amount'] }}" @else value="{{ old('min_amount') }}" @endif />
                  </div>
                  <div class="form-group col-md-6">
                      <label for="amount">Enter Max Price Range</label>
                      <input type="text" placeholder="Enter Max Amount" name="max_amount" autocomplete="off"  class="form-control" @if(isset($coupon['max_amount'])) value="{{ $coupon['max_amount'] }}" @else value="{{ old('max_amount') }}" @endif/>
                  </div>
                  <div class="form-group col-md-12">
                      <label for="categories">Select Categories</label>
                      <select name="categories[]" id="e1" class="form-control selectbox MultipleSelect select2" required multiple  style="color:#000;" data-actions-box="true">
                        @foreach($categories as $category)
                          <!-- <optgroup label="{{ $category['category_name'] }}"></optgroup> -->
                          <option value="{{ $category['id'] }}" @if(in_array($category['id'],$selCats)) selected="" @endif>{{ $category['category_name']}}</option>
                          @foreach($category['subcategories'] as $subcategory)
                            <option value="{{ $subcategory['id'] }}" @if(in_array($subcategory['id'],$selCats)) selected="" @endif>&nbsp;&nbsp;&nbsp;--&nbsp;&nbsp;{{ $subcategory['category_name']}}</option>
                            @foreach($subcategory['subcategories'] as $subsubcategory)
                              <option value="{{ $subsubcategory['id'] }}" @if(in_array($subsubcategory['id'],$selCats)) selected="" @endif>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;--&nbsp;{{ $subsubcategory['category_name']}}</option>
                            @endforeach  
                          @endforeach
                        @endforeach
                      </select>
                      <!-- <span class="selectall"><input type="checkbox" id="cat_checkbox" >Select All</span><a class="clearAllData" href="javascript:;">Clear</a> -->
                      <span class="btn btn-success btn-sm select-all" style="margin-top:8px;">{{ __('Select all') }}</span>
                      <span class="btn btn-danger btn-sm deselect-all" style="margin-top:8px;">{{ __('Deselect all') }}</span>
                  </div>
                  <div class="form-group col-md-6" style="display:none">
                      <label for="brands">Select Brands</label>
                      <select name="brands[]" id="b1" class="form-control selectbox MultipleSelect select2"  multiple>
                          @foreach($brands as $brand)
                            <option value="{{ $brand['id'] }}" @if(in_array($brand['id'],$selBrands)) selected="" @endif>{{ $brand['brand_name']}}</option>
                          @endforeach
                      </select>
                      <!-- <span class="brandselectall"><input type="checkbox" id="brand_checkbox" >Select All</span> -->
                      <span class="btn btn-success btn-sm select-all" style="margin-top:8px;">{{ __('Select all') }}</span>
                      <span class="btn btn-danger btn-sm deselect-all" style="margin-top:8px;">{{ __('Deselect all') }}</span>
                  </div>
                  <?php /* <div class="form-group col-md-6">
                      <label for="users">Select Users</label>
                      <select name="users[]" id="u1" class="form-control text-dark selectbox MultipleSelect select2" multiple="">
                      @foreach($users as $user)
                        <option value="{{ $user['email'] }}" @if(in_array($user['email'],$selUsers)) selected="" @endif>{{ $user['email'] }}</option>  
                      @endforeach
                    </select>
                    (Leave if all Users to Select)
                    <!-- <span class="userselectall"><input type="checkbox" id="user_checkbox" >Select All</span> -->
                    <!-- <span class="btn btn-success btn-sm select-all" style="margin-top:8px;">{{ __('Select all') }}</span> --><br>
                      <span class="btn btn-danger btn-sm deselect-all" style="margin-top:8px;">{{ __('Deselect all') }}</span>
                  </div> */ ?>
				   <div class="form-group col-md-6">
                      <label for="expiry_date">Start Date</label>
                      <input type="date" class="form-control" name="start_date" id="start_date" placeholder="Enter Start Date" @if(isset($coupon['start_date'])) value="{{ $coupon['expiry_date'] }}" @else value="{{ old('start_date') }}" @endif required>
                  </div>
                  <div class="form-group col-md-6">
                      <label for="expiry_date">Expiry Date</label>
                      <input type="date" class="form-control" name="expiry_date" id="expiry_date" placeholder="Enter Expiry Date" @if(isset($coupon['expiry_date'])) value="{{ $coupon['expiry_date'] }}" @else value="{{ old('expiry_date') }}" @endif required>
                  </div>
                  <div class="form-group col-md-6">
                        <label for="visible">Visible in Cart:
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                        <input style="margin-top:7px;" type="checkbox" class="form-check-input" name="visible" value="1" <?php  if(!empty($coupon['visible']) && $coupon['visible']=="1") { echo "checked"; } ?> />
                  </div>
                </div>
                </div>
                <!-- /.card-body -->

                <div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </form>
                <!-- /.form-group -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
          <!-- /.card-body -->
          <div class="card-footer">
          </div>
        </div>
        <!-- /.card -->

        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>

@endsection