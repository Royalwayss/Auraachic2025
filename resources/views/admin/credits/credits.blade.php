@extends('admin.layout.layout')
@section('content')

<style>
    /* Center align all table cells */
    #credits th, #credits td {
        text-align: center;
        vertical-align: middle;
    }

    /* Define column widths (except ID) */
    #credits th:nth-child(1), #credits td:nth-child(1) { width: 5%; }   /* ID */
    #credits th:nth-child(2), #credits td:nth-child(2) { width: 15%; }  /* User */
    #credits th:nth-child(3), #credits td:nth-child(3) { width: 10%; }  /* Amount */
    #credits th:nth-child(4), #credits td:nth-child(4) { width: 15%; }  /* Details */
    #credits th:nth-child(5), #credits td:nth-child(5) { width: 15%; }  /* Remarks */
    #credits th:nth-child(6), #credits td:nth-child(6) { width: 10%; }  /* Expiry Date */
    #credits th:nth-child(7), #credits td:nth-child(7) { width: 10%; }  /* Order ID */
    #credits th:nth-child(8), #credits td:nth-child(8) { width: 10%; }  /* Created By */
    #credits th:nth-child(9), #credits td:nth-child(9) { width: 10%; }  /* Created On */
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Wallet Management</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Credits</li>
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
            @if(Session::has('flash_message_success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <strong>Success:</strong> {{ Session::get('flash_message_success') }}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            @if(Session::has('flash_message_error'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
              <strong>Error!</strong> {!! session('flash_message_error') !!}
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            @endif
            <div class="card">
              @if($creditsModule['edit_access']==1 || $creditsModule['full_access']==1)
              <div class="card-header">
                <h3 class="card-title">Credits</h3>
                <a style="max-width: 150px; float:right; display: inline-block;" href="{{ url('admin/add-credit') }}" class="btn btn-block btn-primary">Add Credit</a>
              </div>
              @endif
              <!-- /.card-header -->
              <div class="card-body">
                <table id="credits" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Details</th>
                            <th>Amount</th>
                            <th>Credit Details</th>
                            <th>Remarks</th>
                            <th>Expiry Date</th>
                            <th>Order ID</th>
                            <th>Created By</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($credits as $credit)
                            @php
                                $isDebit = $credit['type'] === "debit";
                                $rowColor = $isDebit ? "#f8d7da" : "#d4edda"; // Red for debit, green for credit
                                $textColor = $isDebit ? "#721c24" : "#155724"; // Text color
                                $icon = $isDebit 
                                    ? '<i class="fas fa-minus-circle text-danger" title="Debited"></i>' 
                                    : '<i class="fas fa-plus-circle text-success" title="Credited"></i>';
                            @endphp

                            <tr style="background-color: {{ $rowColor }}; color: {{ $textColor }}; opacity: 0.9;">
                                <!-- Credit ID -->
                                <td><b>{{ $credit['id'] }}</b></td>

                                <!-- User ID with Link -->
                                <td>
                                    <strong>User ID:</strong> <span style="color: {{ $textColor }};">{!! $credit['user_id_link'] !!}</span>
                                    <br>
                                    <strong>Email:</strong> <span style="color: {{ $textColor }};">{!! $credit['user_email_link'] !!}</span>
                                </td>

                                <!-- Credit Amount with Currency Icon -->
                                <td>
                                    <div>{!! $icon !!} ₹{{ number_format($credit['amount'], 2) }}</div>
                                    <!-- <i class="fas fa-money-bill-wave"></i> --> 
                                </td>

                                <!-- Amount Description -->
                                <td>
                                    @if(!empty($credit['action']))
                                        <i class="fas fa-info-circle"></i> {{ $credit['action'] }}
                                    @endif
                                </td>

                                <!-- Remarks with Expiry Status -->
                                <td>
                                    @if(!empty($credit['remarks']))
                                        <i class="fas fa-comment-alt"></i> {{ $credit['remarks'] }}
                                    @endif
                                    @if($credit['is_expired'] === "Yes")
                                        <br><font color="red"><i class="fas fa-exclamation-circle"></i> Expired</font>
                                    @endif
                                </td>

                                <!-- Expiry Date -->
                                <td>
                                    @if($credit['expiry_date']!="0000-00-00" && !empty($credit['expiry_date']))
                                        <i class="fas fa-calendar-alt"></i> {{ $credit['expiry_date'] }}
                                    @endif
                                </td>

                                <!-- Order ID with Link -->
                                <td>
                                    @if(!empty($credit['user_order_link']))
                                        <i class="fas fa-shopping-cart"></i>
                                        <span style="color: {{ $textColor }};">{!! $credit['user_order_link'] !!}</span>
                                    @endif
                                </td>

                                <!-- Created By -->
                                <td><i class="fas fa-user-shield"></i> {{ ucwords($credit['created_by']) }}</td>

                                <!-- Created On -->
                                <td><i class="fas fa-clock"></i> {{ date("F j, Y, g:i a", strtotime($credit['created_at'])) }}</td>
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