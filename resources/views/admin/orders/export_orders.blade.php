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
          <div class="col-sm-6">
            <h1>Orders</h1>
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
        <div class="row">
          <div class="col-md-6">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Export Order</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <form  role="form"  class="form-horizontal" method="post" action="{{url('/admin/export-orders')}}"> 
                            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                            <div class="form-body"> 
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Select Status :</label>
                                    <div class="col-md-5">
                                        <select name="status[]" class="form-control" multiple="" required>
                                            @foreach($orderstatuses as $ostatus)
                                                <option value="{{ $ostatus['name']}}">{{ $ostatus['name']}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Export Type :</label>
                                    <div class="col-md-5">
                                        <select name="type" class="form-control">
                                            <option value="Orders">Orders</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class="col-md-3 control-label">From Date: </label>
                                    <div class="col-md-5">
                                        <input name="from_date" type="date" autocomplete="off" placeholder="From Date"  style="color:gray" class="form-control datePicker"/>
                                    </div>
                                </div>
                                <div class="form-group ">
                                    <label class="col-md-3 control-label">To Date: </label>
                                    <div class="col-md-5">
                                        <input name="to_date" type="date" autocomplete="off" placeholder="To Date"  style="color:gray" class="form-control datePicker"/>
                                    </div>
                                </div>           
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

           
            <!-- /.card -->
          </div>
          <!-- /.col -->
          
            <!-- /.card -->
          </div>
          <!-- /.col -->
        </div>
        <!-- /.row -->
        
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
@endsection