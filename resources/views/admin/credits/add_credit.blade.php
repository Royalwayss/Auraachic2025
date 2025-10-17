@extends('admin.layout.layout')
@section('content')
<style>
  .select2-container--default .select2-selection--single
  {
    background-color:transparent !important;

    display: block;
    width: 100%;
    height: calc(2.25rem + 2px);
    padding: 0.375rem 0.75rem;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
    box-shadow: inset 0 0 0 transparent;
    transition: border-color .15s ease-in-out,box-shadow .15s ease-in-out;
    
  }

  .select2-container--default .select2-selection--single .select2-selection__rendered
  {
    color:#fff !important;
  }
</style>
<!-- Inline Styles for Autocomplete Dropdown -->
<!-- Inline Styles for Position Fix -->
<style>
    /* Ensure the autocomplete dropdown stays near the input field */
    .ui-autocomplete {
        position: absolute !important;
        z-index: 1050 !important;
        max-height: 200px;
        overflow-y: auto;
        overflow-x: hidden;
        background-color: #ffffff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .ui-menu-item {
        padding: 8px 12px;
        font-size: 14px;
        color: #333;
        cursor: pointer;
    }

    .ui-menu-item:hover {
        background-color: #f5f5f5;
    }

    /* Override any default positioning issues */
    .form-group {
        position: relative !important; /* Ensures the dropdown aligns with the field */
    }

    

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #000 !important;
}
</style>
<!-- Include jQuery and jQuery UI -->
<!-- Include jQuery -->
<?php /* <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> */ ?>

<!-- Include select2 CSS and JS -->
<?php /* <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" /> */ ?>
<?php /* <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script> */ ?>
<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Wallet Management</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
              <li class="breadcrumb-item active">Add Credit</li>
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
            <h3 class="card-title">Add Credit</h3>

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
                <form name="creditForm" id="creditForm" action="{{ url('admin/add-credit') }}" method="post" enctype="multipart/form-data">@csrf
                  <div class="card-body">
                      <div class="form-group col-md-6">
                          <label for="user_email">Specify User*</label>
                          <select id="email_search" name="user_email" class="form-control" style="width: 100%;" required>
                              <option value="">Type to search emails</option>
                          </select>
                      </div>
                      <div class="form-group col-md-6">
                          <label for="amount">Amount*</label>
                          <input type="number" class="form-control" id="amount" name="amount" required>
                      </div>
                      <div class="form-group col-md-6">
                          <label for="remarks">Remarks</label>
                          <input type="text" class="form-control" id="remarks" name="remarks">
                      </div>
                      <!-- <div class="form-group col-md-6">
                          <label for="order_id">Order ID</label>
                          <input type="text" class="form-control" id="order_id" name="order_id">
                      </div> -->
                      <div class="form-group col-md-6">
                          <label for="order_id">Specify Order</label>
                          <select id="order_search" name="order_id" class="form-control" style="width: 100%;">
                            <option value="">Type to search orders</option>
                          </select>
                      </div>
                      <div class="form-group col-md-6">
                          <label for="expiry_date">Expiry Date</label>
                          <input type="date" class="form-control" id="expiry_date" name="expiry_date" required>
                      </div>
                  </div>
                  <!-- /.card-body -->

                  <div>
                      <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
              </form>
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