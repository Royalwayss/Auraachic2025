@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Website Settings</h1>
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
                @foreach($errors->all() as $error)
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin:10px;">
                  <strong>Error:</strong> {!!$error!!}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endforeach
                @if(Session::has('error_message'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Error:</strong> {{ Session::get('error_message') }}
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
				
				
				
				
                <form name="subadminForm" id="websiteSettingForm" action="{{ url('admin/save-website-settings') }}" method="post" enctype="multipart/form-data">@csrf
              
                <div class="card-body">
                <div class="row">
                  <div class="form-group col-md-6">
                    <label for="invoice_email">Invoice Email</label>
                    <input  type="text" class="form-control" id="invoice_email" name="invoice_email" @if(isset($website_settings['invoice_email']) && $website_settings['invoice_email']!="") value="{{ $website_settings['invoice_email'] }}" @else value="{{ @old('invoice_email') }}" @endif >
                  </div>
                  <div class="form-group col-md-6">
                    <label for="invoice_mobile">Invoice Contact No</label>
                    <input  type="text" class="form-control" id="invoice_mobile" name="invoice_mobile" @if(isset($website_settings['invoice_mobile']) && $website_settings['invoice_mobile']!="") value="{{ $website_settings['invoice_mobile'] }}"  @else value="{{ @old('invoice_mobile') }}" @endif >
                  </div>
				   <div class="form-group col-md-12">
                    <label for="invoice_address">Invoice Address</label>
                    <input  type="text" class="form-control" id="invoice_address" name="invoice_address" @if(isset($website_settings['invoice_address']) && $website_settings['invoice_address']!="") value="{{ $website_settings['invoice_address'] }}"  @else value="{{ @old('invoice_address') }}" @endif >
                  </div>
                  <div class="form-group col-md-6">
                    <label for="invoice_logo">Invoice Logo*</label>
                    <input type="file" class="form-control" id="invoice_logo" name="invoice_logo" >
                  </div>
				  @if(isset($website_settings['invoice_logo']) && $website_settings['invoice_logo']!="")
				  <div class="form-group col-md-6">
                    <label for="invoice_logo">Uploaded Invoice Logo</label><br>
                    <img src="{{ asset('front/images/website_settings/'.$website_settings['invoice_logo']) }}">
                  </div>
				  @endif
				  
				  
                 
                 
                </div>
                </div>
                <!-- /.card-body -->

                <div class="form-group col-md-6">
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