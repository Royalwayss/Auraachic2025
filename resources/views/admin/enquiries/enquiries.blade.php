@extends('admin.layout.layout')
@section('content')

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Enquiries</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">Contact Enquiries</li>
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
              <div class="card-header">
                <h3 class="card-title">Contact Enquiries</h3>
                <a style="float:right" target="_blank" href="{{url('admin/export-enquiries')}}" class="btn btn-primary">Export Enquiries</a>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="enquiries" class="table table-bordered table-striped">
                  <thead>
                  <tr>
                    <th>ID</th>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Mobile</th>
                      <th>Message</th>
                      <th>Date/Time</th>
                  </tr>
                  </thead>
                  <tbody>
                    @foreach($enquiries as $enquiry)
                  <tr>
                    <td>{{ $enquiry['id'] }}</td>
                      <td>{{ $enquiry['name'] }}</td>
                      <td>{{ $enquiry['email'] }}</td>
                      <td>{{ $enquiry['mobile'] }}</td>
                      <td>{{ $enquiry['message'] }}</td>
                    <td>{{ date("F j, Y, g:i a", strtotime($enquiry['created_at'])); }}</td>
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