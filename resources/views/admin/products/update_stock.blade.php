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
              <li class="breadcrumb-item active">Update Product Stock</li>
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
            <h3 class="card-title">Update Product Stock</h3>

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


                  <form id="updateStockForm" role="form" class="form-horizontal" method="post" action="{{ url('admin/update-stock') }}" enctype="multipart/form-data"> 
                            <input type="hidden" name="_token" value="{{{ csrf_token() }}}" />
                            <div class="form-body">
                                <div class="form-group">
                                    <label class="col-md-3 control-label">Upload Csv File:</label>
                                    <div class="col-md-5">
                                        <input type="file" name="stock_file" required />
                                    </div>        
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"></label>
                                    <div class="col-md-6">
                                        <p style="font-size: 16px; font-weight: bold; color: #333; margin-bottom: 10px;">
                                            ✅ Demo Format for CSV File (SKU and Stock column names are Case Sensitive):
                                        </p>
                                        <table style="width: 100%; border-collapse: collapse; border: 1px solid #ccc; font-family: Arial, sans-serif;">
                                            <thead>
                                                <tr style="background-color: #f4f4f4; color: #333;">
                                                    <th style="padding: 10px; border: 1px solid #ccc; text-align: center;">SKU</th>
                                                    <th style="padding: 10px; border: 1px solid #ccc; text-align: center;">Stock</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: center;">OV573-B-S</td>
                                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: center;">10</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: center;">OV573-B-M</td>
                                                    <td style="padding: 10px; border: 1px solid #ccc; text-align: center;">20</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="form-actions right1 text-center">
                                <button class="btn btn-primary" type="submit">Update Stock</button>
                            </div>

                        </form>
                  
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