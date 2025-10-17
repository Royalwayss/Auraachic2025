@extends('admin.layout.layout')
@section('content')

<style>
.info-box:hover {
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  transform: translateY(-5px);
  transition: all 0.3s ease-in-out;
  background-color: #f0f8ff; /* Light background color on hover */
}

.info-box .info-box-text,
.info-box .info-box-number {
  transition: all 0.3s ease-in-out;
}

.info-box:hover .info-box-text,
.info-box:hover .info-box-number {
  font-weight: bold; /* Make text bold */
  color: #007bff; /* Highlight text with primary color */
}

.info-box .info-box-icon:hover {
  background-color: rgba(0, 123, 255, 0.8); /* Change icon background on hover */
}

</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  @if(Session::has('error_message'))
    <div class="alert alert-danger alert-dismissible fade show mx-3" role="alert">
      <strong>Error:</strong> {{ Session::get('error_message') }}
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
  @endif

  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <!-- Info boxes -->
      <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
          <a href="{{ url('admin/categories') }}">
          <div class="info-box">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-tasks"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Categories</span>
              <span class="info-box-number">
                {{ $categoriesCount }}
                <!-- <small>%</small> -->
              </span>
            </div>
            <!-- /.info-box-content -->
          </div></a>
          <!-- /.info-box -->
        </div>
       

        <!-- fix for small devices only -->
        <div class="clearfix hidden-md-up"></div>

        <div class="col-12 col-sm-6 col-md-3">
          <a href="{{ url('admin/products') }}">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-tshirt"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Products</span>
              <span class="info-box-number">{{ $productsCount }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          </a>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
          <a href="{{ url('admin/users') }}">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Users</span>
              <span class="info-box-number">{{ $usersCount }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          </a>
          <!-- /.info-box -->
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <a href="{{ url('admin/coupons') }}">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-primary elevation-1"><i class="fas fa-rupee-sign"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Coupons</span>
              <span class="info-box-number">{{ $couponsCount }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          </a>
          <!-- /.info-box -->
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <a href="{{ url('admin/orders') }}">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-dark elevation-1" style="background-color:purple;"><i class="fas fa-list"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Orders</span>
              <span class="info-box-number">{{ $ordersCount }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          </a>
          <!-- /.info-box -->
        </div>
        <div class="col-12 col-sm-6 col-md-3">
          <a href="{{ url('admin/cms-pages') }}">
          <div class="info-box mb-3">
            <span class="info-box-icon bg-muted elevation-1" style="background-color:#cdf0f7"><i class="fas fa-copy"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Pages</span>
              <span class="info-box-number">{{ $pagesCount }}</span>
            </div>
            <!-- /.info-box-content -->
          </div>
          </a>
          <!-- /.info-box -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </div><!--/. container-fluid -->

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@endsection