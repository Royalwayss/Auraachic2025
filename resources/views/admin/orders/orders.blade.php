@extends('admin.layout.layout')
@section('content')
<style>

/* Action Icon Hover Effect */
a .fas {
  transition: color 0.3s ease, transform 0.3s ease;
}

a:hover .fas {
  color: #1E90FF; /* Vibrant blue color on hover */
  transform: scale(1.2); /* Slightly enlarge icon */
}

/* Action Icons for Better Visibility */
a .fas.fa-toggle-on {
  color: #28a745; /* Green for Active */
}

a .fas.fa-toggle-off {
  color: #dc3545; /* Red for Inactive */
}

a .fas.fa-edit {
  color: #ffc107; /* Yellow for Edit */
}

a .fas.fa-trash {
  color: #dc3545; /* Red for Delete */
}

a .fas.fa-unlock {
  color: #17a2b8; /* Teal for Update Role */
}

</style>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Orders Management</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Orders</li>
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
            @if(Session::has('error_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Error:</strong> {{ Session::get('error_message') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            <div class="card">
              @if($ordersModule['edit_access']==1 || $ordersModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">Orders</h3>
                <a style="float:right" target="_blank" href="{{url('admin/export-orders')}}" class="btn btn-primary">Export Orders</a>
              </div>
              @endif
              <!-- /.card-header -->
              <div class="card-body">
                <table id="orders" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>Order ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Order Status</th>
                    <th>Order Date</th>
                    <th>Actions</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($orders as $order)
                  <tr>
                    <td>{{ $order['id'] }}</td>
                    <td>{{ $order['name'] }}</td>
                    <td>{{ $order['email'] }}</td>
                    <td>{{ $order['mobile'] }}</td>
                    <td>₹{{ round($order['grand_total'],2) }}</td>
                    <td>{{ ucfirst($order['payment_method']) }}</td>
                    <td>{{ $order['order_status'] }}</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($order['created_at'])); }}</td>
                    <td>
                      <!-- @if($ordersModule['edit_access']==1 || $ordersModule['full_access']==1) -->
                        &nbsp;&nbsp;
                        <a style='color:#3f6ed3;' href="{{ url('admin/orders/'.$order['id']) }}"><i class="fas fa-file"></i></a>
                        &nbsp;&nbsp;
                        <a target="_blank" style='color:#3f6ed3;' href="{{ url('admin/orders/invoice/'.$order['id']) }}"><i class="fas fa-print"></i></a>
                        &nbsp;&nbsp;
                        <a target="_blank" style='color:#3f6ed3;' href="{{ url('admin/orders/print-invoice/'.$order['id']) }}"><i class="fas fa-print" style="color:red;"></i></a>
                        <!-- @endif -->
                        @if($ordersModule['full_access']==1)
                        <!-- <a style='color:#3f6ed3;' class="confirmDelete" title="Delete Order" href="javascript:void(0)" record="order" recordid="{{ $order['id'] }}"><i class="fas fa-trash"></i></a>   -->
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