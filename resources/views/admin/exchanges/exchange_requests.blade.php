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
          <h1 class="m-0">Order Management</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Exchange Requests</li>
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
              <!-- /.card-header -->
              @if($exchangeModule['edit_access']==1 || $exchangeModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">Exchange Requests</h3>
                <!-- <a style="max-width: 150px; float:right; display: inline-block;" href="{{ url('admin/export-exchanges') }}" class="btn btn-block btn-primary">Export Exchanges</a>&nbsp; -->
              </div>
              @endif
              <div class="card-body">
                <table id="exchanges" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th style="width: 10%">
                          Exchange ID
                      </th>
                      <th style="width: 10%">
                          Exchange Date
                      </th>
                      <th>
                          Order ID
                      </th>
                      <th>
                          User ID
                      </th>
                      <th>
                          Product Code
                      </th>
                      <th>
                          Current Size
                      </th>
                      <th>
                          Requested Size
                      </th>
                      <th>
                          Exchange Reason
                      </th>
                      <th>
                          Exchange Status
                      </th>
                      <th>
                          Approve/Reject
                      </th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($exchangeRequests as $exchange)
                  <tr>
                    <td>{{ $exchange['id'] }}</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($exchange['created_at'])); }}</td>
                    <td><a href="{{ url('admin/orders/'.$exchange['order']['id']) }}" target="_blank">{{ $exchange['order']['id'] }}</a></td>
                    <td><a href="{{ url('admin/users/?id='.$exchange['user_id']) }}" target="_blank">{{ $exchange['user_id'] }}</a></td>
                    <td><a href="{{ url('admin/add-edit-product/'.$exchange['product_id']) }}" target="_blank">{{ $exchange['product_code'] }}</a></td>
                    <td>{{ $exchange['current_size'] }}</td>
                    <td>{{ $exchange['requested_size'] }}</td>
                    <td>{{ $exchange['exchange_reason'] }}</td>
                    <td>{{ $exchange['exchange_status'] }}</td>
                    <td>
                        @if($exchange['exchange_status']=="Exchange Approved" || $exchange['exchange_status']=="Exchange Rejected")
                          {{ $exchange['exchange_status'] }}<br>
                          @if($exchange['comment']!="")
                            <br>Remarks: {{ $exchange['comment'] }}
                          @endif
                        @else
                        <form action="{{ url('admin/exchange-requests/update') }}" method="post">@csrf
                          <input type="hidden" name="order_id" value="{{ $exchange['order']['id'] }}">
                          <input type="hidden" name="exchange_id" value="{{ $exchange['id'] }}">
                          <input type="hidden" name="product_code" value="{{ $exchange['product_code'] }}">
                          <select class="form-control form-filter input-sm" name="exchange_status" style="width: 150px;" required>
                            <option value="">Select</option>
                            <option value="Exchange Approved">Approved</option>
                            <option value="Exchange Rejected">Rejected</option>
                            <!-- <option value="Exchange Pending">Pending</option> -->
                        </select><br>
                          <textarea class="form-control" placeholder="Comments" type="text" name="comment"></textarea><br>
                          <input style="margin-top: 5px;" type="submit" value="Update">
                        </form>
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