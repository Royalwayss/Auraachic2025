<?php use App\Models\Product; ?>
@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Custom Fits</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Custom Fits</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->

  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            @if(Session::has('success_message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success:</strong> {{ Session::get('success_message') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            <div class="card">
              @if($customfitModule['edit_access']==1 || $customfitModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">View Custom Fit</h3>
             <!--   <div class="text-right">
                  <a style="max-width: 150px; margin-top: 0px ; display: inline-block;" href="{{ url('admin/import-data') }}" class="btn btn-block btn-primary">Import Reviews</a> 
                </div> -->
              </div>
              @endif
              <!-- /.card-header -->
              <div class="card-body">
                <table id="customfits" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Product</th>
                    <th>Title</th>
                    <th>Message</th>
                    <th>Added on</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($customfits as $customfit)
                  <tr> 
                    
                    <td>
                    
                      <a target="_blank" href="{{ route('product',[$customfit['product']['product_url'],$customfit['product_id']]) }}">{{ $customfit['product']['product_name'] }}</a>
                    </td>
                    <td>{{ $customfit['title'] }}</td>
                    <td>{{ $customfit['mobile'] }}</td>
                    <td>{{ $customfit['message'] }}</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($customfit['created_at'])); }}</td>
                   
                  </tr>
                  @endforeach
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

@endsection