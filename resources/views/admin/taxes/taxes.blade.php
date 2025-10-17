@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Taxes</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Taxes</li>
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
              @if($taxesModule['edit_access']==1 || $taxesModule['full_access']==1)
              <!-- <div class="card-header">
                <h3 class="card-title">View Subscribers</h3>
                <a style="max-width: 150px; float:right; display: inline-block;" href="{{ url('admin/add-edit-taxe') }}" class="btn btn-block btn-primary">Add Subscriber</a>
              </div> -->
              @endif
              <!-- /.card-header -->
              <form action="{{ url('admin/update-taxes') }}" method="post">@csrf
              <div class="card-body">
                
                  <table id="taxes" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                      <th>Province</th>
                      <th>Tax</th>
                      <!-- <th>Actions</th> -->
                    </tr>
                    </thead>
                    <tbody>
                      @foreach($taxes as $tax)
                    <tr>
                      <td><input type="hidden" name="state[]" value="{{ $tax['name'] }}">{{ $tax['name'] }}</td>
                      <td><input type="text" name="tax[]" value="{{ $tax['taxes'] }}"></td>
                      <!-- <td>
                        @if($taxesModule['edit_access']==1 || $taxesModule['full_access']==1)
                        @if($tax['status']==1)
                            <a class="updateSubscriberStatus" id="taxe-{{ $tax['id'] }}" taxe_id="{{ $tax['id'] }}" style='color:#3f6ed3' href="javascript:void(0)"><i class="fas fa-toggle-on" status="Active"></i></a>
                          @else
                            <a class="updateSubscriberStatus" id="taxe-{{ $tax['id'] }}" taxe_id="{{ $tax['id'] }}" style="color:grey" href="javascript:void(0)"><i class="fas fa-toggle-off" status="Inactive"></i></a>
                          @endif
                        @endif
                        
                      </td> -->
                    </tr>
                    @endforeach
                    </tbody>
                  </table>
                
              </div>
              <div class="form-group col-md-6">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </form>
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