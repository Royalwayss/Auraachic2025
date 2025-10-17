@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            @if(isset($_GET['module'])&&$_GET['module']!="")
                <h1>{{ $_GET['module'] }}</h1>
            @else
                <h1>Products Management</h1>
            @endif
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
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
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
                @if(Session::has('flash_message_success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <strong>Success:</strong> {{ Session::get('flash_message_success') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif
                @if(Session::has('flash_message_error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <strong>Error:</strong> {{ Session::get('flash_message_error') }}
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                </div>
                @endif

                <div class="card-body">


                  @if(isset($_GET['type']) && !empty($_GET['type']) && isset($_GET['filename']) && !empty($_GET['filename']))
                            <form class="form-horizontal" method="post" action="{{url('/admin/import-file-data')}}">@csrf
                                <div class="form-group">
                                    <label class="col-md-3 control-label">File :</label>
                                    <div class="col-md-4">
                                        <input class="form-control" type="text" name="filename" value="{{$_GET['filename']}}" readonly>
                                    </div>
                                </div>
                                <input type="hidden" name="type" value="{{$_GET['type']}}">
                                <div class="form-actions right1 text-center">
                                    <button type="submit" class="btn btn-primary">Import Now</button>
                                </div>
                            </form>
                        @else
                            <form  role="form" class="form-horizontal" method="post" enctype="multipart/form-data" action="{{url('/admin/import-data')}}"> 
                                <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                                <div class="form-body"> 
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Select Import Type :</label>
                                        <div class="col-md-4">
                                            <select class="form-control" name="type" required>
                                                <option value="">Please Select</option>
                                                <option value="products">Import Products</option>
                                                <!-- <option value="reviews">Import Reviews</option> -->
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Choose File :</label>
                                        <div class="col-md-4">
                                            <input type="file" name="file" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Samples:</label>
                                        <div class="col-md-6">
                                            <table class="table table-striped table-bordered table-hover">
                                                <tbody>
                                                    <tr>
                                                        <th>Import</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                    <tr>
                                                        <td>Import Products</td>
                                                        <td><a href="{{url('/admin/samples/products.xls')}}">Download Sample</a></td>
                                                    </tr>
                                                    <!-- <tr>
                                                        <td>Import Reviews</td>
                                                        <td><a href="{{url('/admin/samples/reviews.xls')}}">Download Sample</a></td>
                                                    </tr> -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"></label>
                                        <div class="col-md-9">
                                            <b>Note:- Please Upload required formats of xls. we have give option of download sample format above.</b>
                                            <br>
                                            <b>Important Note:- Please add minimum 1 and  maximum 100 rows in one xls file.</b>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-actions right1 text-center">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        @endif
                  
                </div>
                <!-- /.card-body -->

       
              
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