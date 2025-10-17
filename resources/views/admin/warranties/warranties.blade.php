@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Warranties</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Warranties</li>
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
              @if($warrantiesModule['edit_access']==1 || $warrantiesModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">View Warranties</h3>
              </div>
              @endif
              <!-- /.card-header -->
              <div class="card-body">
                <table id="warranties" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Date Purchased</th>
                    <th>Purchased from</th>
                    <th>Receipt</th>
                    <th>Created on</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($warranties as $warranty)
                  <tr>
                    <td>{{ $warranty['id'] }}</td>
                    <td>{{ $warranty['name'] }}<br>{{ $warranty['email'] }}
                      <br>{{ $warranty['street'] }}, {{ $warranty['city_state'] }}-{{ $warranty['postal_code'] }}
                    </td>
                    <td>{{ $warranty['order_product_id'] }}
                    </td>
                    <td>{{ $warranty['date_purchased'] }}</td>
                    <td>{{ $warranty['purchased_from'] }}</td>
                    <td><a target="_blink" href="{{ url('front/images/receipts/'.$warranty['receipt']) }}">View</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($warranty['created_at'])); }}</td>
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