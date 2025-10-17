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
            <li class="breadcrumb-item active">Return Requests</li>
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
              @if($returnModule['edit_access']==1 || $returnModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">Return Requests</h3>
                <!-- <a style="max-width: 150px; float:right; display: inline-block;" href="{{ url('admin/export-returns') }}" class="btn btn-block btn-primary">Export Returns</a>&nbsp; -->
              </div>
              @endif
              <div class="card-body">
                <table id="returns" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th style="width: 10%">
                          Return ID
                      </th>
                      <th style="width: 10%">
                          Return Date
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
                          Return Reason
                      </th>
                      <th>
                          Return Status
                      </th>
                      <th>
                          Payment Method
                      </th>
                      <!-- <th>
                          Uniware Response
                      </th> -->
                      <th>
                          Account Details
                      </th>
                      <th>
                          Approve/Reject
                      </th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($returnRequests as $return)
                  <tr>
                    <td>{{ $return['id'] }}</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($return['created_at'])); }}</td>
                    <td><a href="{{ url('admin/orders/'.$return['order']['id']) }}" target="_blank">{{ $return['order']['id'] }}</a></td>
                    <td><a href="{{ url('admin/users/?id='.$return['user_id']) }}" target="_blank">{{ $return['user_id'] }}</a></td>
                    <td><a href="{{ url('admin/add-edit-product/'.$return['product_id']) }}" target="_blank">{{ $return['product_code'] }}</a></td>
                    <td>{{ $return['return_reason'] }}</td>
                    <td>{{ $return['return_status'] }}</td>
                    <td>{{ $return['payment_method'] }}</td>
                    <!-- <td>{{ $return['pushed_response'] }}</td> -->
                    <td>@if(isset($return['account']['id']))
                          Bank Name: {{ ucwords($return['account']['bank_name']) }}<br>
                          Account Holder Name: {{ ucwords($return['account']['account_holder_name']) }}<br>
                          Account Number: {{ ucwords($return['account']['account_number']) }}<br>
                          Account Type: {{ ucwords($return['account']['account_type']) }}<br>
                          IFSC Code: {{ ucwords($return['account']['ifsc_code']) }}<br>
                        @endif
                    </td>
                    <td>
                        @if($return['return_status']=="Return Approved" || $return['return_status']=="Return Rejected")
                          {{ $return['return_status'] }}<br>
                          @if($return['comment']!="")
                            <br>Remarks: {{ $return['comment'] }}
                          @endif
                        @else
                        <form action="{{ url('admin/return-requests/update') }}" method="post">@csrf
                          <input type="hidden" name="return_id" value="{{ $return['id'] }}">
                          <input type="hidden" name="product_code" value="{{ $return['product_code'] }}">
                          <select class="form-control form-filter input-sm" name="return_status" style="width: 150px;" required>
                            <option value="">Select</option>
                            <option value="Return Approved">Approved</option>
                            <option value="Return Rejected">Rejected</option>
                            <!-- <option value="Return Pending">Pending</option> -->
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