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
          <h1 class="m-0">Ratings/Reviews</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Ratings/Reviews</li>
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
              @if($ratingsModule['edit_access']==1 || $ratingsModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">View Ratings/Reviews</h3>
                <div class="text-right">
                  <a style="max-width: 150px; margin-top: 0px ; display: inline-block;" href="{{ url('admin/import-data') }}" class="btn btn-block btn-primary">Import Reviews</a> 
                </div> 
              </div>
              @endif
              <!-- /.card-header -->
              <div class="card-body">
                <table id="ratings" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Product</th>
                    <th>Review</th>
                    <th>Star Rating</th>
                    <th>Added on</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($ratings as $rating)
                  <tr> 
                    <td>{{ $rating['name'] }}</td>
                    <td>{{ $rating['email'] }}</td>
                    <td>
                    
                      <a target="_blank" href="{{ route('product',[$rating['product']['product_url'],$rating['product_id']]) }}">{{ $rating['product']['product_name'] }}</a>
                    </td>
                    <td>{{ $rating['review'] }}</td>
                    <td>{{ $rating['rating'] }}</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($rating['created_at'])); }}</td>
                    <td>
                      @if($ratingsModule['edit_access']==1 || $ratingsModule['full_access']==1)
                      @if($rating['status']==1)
                          <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}" rating_id="{{ $rating['id'] }}" style='color:#3f6ed3' href="javascript:void(0)"><i class="fas fa-toggle-on" status="Active"></i></a>
                        @else
                          <a class="updateRatingStatus" id="rating-{{ $rating['id'] }}" rating_id="{{ $rating['id'] }}" style="color:grey" href="javascript:void(0)"><i class="fas fa-toggle-off" status="Inactive"></i></a>
                        @endif
                      @endif
                      @if($ratingsModule['edit_access']==1 || $ratingsModule['full_access']==1)
                        <!-- &nbsp;&nbsp;
                        <a style='color:#3f6ed3;' href="{{ url('admin/add-edit-rating/'.$rating['id']) }}"><i class="fas fa-edit"></i></a>
                        &nbsp;&nbsp; -->
                        @endif
                        @if($ratingsModule['full_access']==1)
                          &nbsp;
                          <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Review" href="javascript:void(0)" record="rating" recordid="{{ $rating['id'] }}"><i class="fas fa-trash"></i></a>
                        @endif  
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