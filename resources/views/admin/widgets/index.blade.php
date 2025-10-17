<?php use App\Models\Product; ?>
@extends('admin.layout.layout')
@section('content')

<style>

ul.widget-list {
    list-style: none;
    padding: 0;
    margin: 0;
}
ul.widget-list li {
    margin-bottom: 5px;
    padding: 10px;
    border: 1px solid #ccc;
    background: #f9f9f9;
    border-radius: 4px;
    position: relative;
}
.drag-handle {
    cursor: grab;
    font-size: 18px;
    margin-right: 10px;
}
.drag-handle:active {
    cursor: grabbing;
}
.child-widget {
    margin-left: 30px;
    display: none;
}
.toggle-arrow-widget {
    cursor: pointer;
    font-size: 18px;
    margin-right: 10px;
}
.widget-actions {
    position: absolute;
    right: 10px;
    top: 10px;
}
.widget-list img {
    border-radius: 4px;
    border: 1px solid #ddd;
    display: inline-block;
}

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

.status-badge {
    font-size: 14px;
    padding: 6px 10px;
    border-radius: 15px; /* Rounded badges */
    display: inline-flex;
    align-items: center;
    gap: 5px; /* Space between icon and text */
}

.status-badge i {
    font-size: 16px; /* Icon size */
}


</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">Widgets Management</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{'/admin/dashboard'}}">Home</a></li>
            <li class="breadcrumb-item active">Widgets</li>
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
                <h3 class="card-title">Widgets</h3>
                <div class="text-right">
                <a style="max-width: 150px; margin-top: 0px ; display: inline-block;" href="{{url('admin/widgets/create')}}" class="btn btn-block btn-primary">Add Widget</a>
                </div>
              </div>
              
              <!-- /.card-header -->
              <div class="card-body">
                <div class="widget-container">
                    <ul class="widget-list" id="sortable">
                        @foreach($widgets as $key => $widget)
                        <li class="parent-widget" data-id="{{ $widget['id'] }}">
                            <span class="toggle-arrow-widget">@if($widget['type'] == "MULTIPLE_BANNERS")▶@endif</span>
                            <span class="drag-handle"><i class="fa fa-arrows-alt"></i></span>
                            @if(isset($widget['widget_media']['desktop_image_url']) && $widget['widget_media']['desktop_image_url'])
                            <img src="{{ $widget['widget_media']['desktop_image_url'] }}" alt="{{ $widget['heading'] }}" style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                            @endif
                            <span class="widget-heading">{{ $widget['heading'] }}</span>
                            <span class="widget-type">({{ $widget['type'] }} @if($widget['section_type']!="") / {{ $widget['section_type'] }} @endif)</span>
                            <span class="widget-status">
                                @if($widget['status'] == 1)
                                    @if($widget['end_date'] > date('Y-m-d'))
                                        <span class="badge badge-success status-badge"><i class="fas fa-check-circle"></i> Active</span>
                                    @else
                                        <span class="badge badge-warning status-badge"><i class="fas fa-exclamation-circle"></i> Expired</span>
                                    @endif
                                @else
                                    <span class="badge badge-secondary status-badge"><i class="fas fa-times-circle"></i> Inactive</span>
                                @endif
                            </span>
                            <div class="widget-actions">
                                <a style='color:#3f6ed3;' href="{{ route('widgets.edit', $widget['id']) }}"><i class="fas fa-edit"></i></a>
                                <form action="{{route('widgets.destroy', $widget['id'])}}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style='color:#dc3545; border:0px; background-color:#fff' onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                            @if($widget['type'] == "MULTIPLE_BANNERS")
                            <ul class="child-widget parent-{{ $widget['id'] }}">
                                @foreach($widget['child_widgets'] as $child)
                                <li data-id="{{ $child['id'] }}" class="child-row">
                                    <span class="drag-handle"><i class="fa fa-arrows-alt"></i></span>
                                    @if(isset($child['widget_media']['desktop_image_url']) && $child['widget_media']['desktop_image_url'])
                                    <img src="{{ $child['widget_media']['desktop_image_url'] }}" alt="{{ $child['heading'] }}" style="width: 30px; height: 30px; object-fit: cover; margin-right: 10px;">
                                    @endif
                                    <span class="widget-heading">{{ $child['heading'] }}</span>
                                    <span class="widget-type">({{ $child['type'] }} / {{ $child['section_type'] }})</span>
                                    <span class="widget-status">
                                        @if($child['status'] == 1)
                                            @if($child['end_date'] > date('Y-m-d'))
                                                <span class="badge badge-success status-badge"><i class="fas fa-check-circle"></i> Active</span>
                                            @else
                                                <span class="badge badge-warning status-badge"><i class="fas fa-exclamation-circle"></i> Expired</span>
                                            @endif
                                        @else
                                            <span class="badge badge-secondary status-badge"><i class="fas fa-times-circle"></i> Inactive</span>
                                        @endif
                                    </span>
                                    <div class="widget-actions">
                                        <a style='color:#3f6ed3;' href="{{ route('widgets.edit', $child['id']) }}"><i class="fas fa-edit"></i></a>
                                        <form action="{{route('widgets.destroy', $child['id'])}}" method="POST" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style='color:#dc3545; border:0px; background-color:#fff' onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            @endif
                        </li>

                        @endforeach
                    </ul>
                </div>
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
<script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
<script>
    $(function () {
    // Sortable for parent widgets
    $("#sortable").sortable({
        handle: ".drag-handle",
        items: "> .parent-widget", // Restrict to parent widgets only
        cancel: ".child-widget, .child-widget li", // Prevent dragging child widgets or child elements
        stop: function () {
            let order = [];
            $("#sortable > .parent-widget").each(function (index) {
                const parentId = $(this).data("id");
                const children = [];

                $(this).find(".child-widget > li").each(function (childIndex) {
                    children.push({
                        id: $(this).data("id"),
                        sort: childIndex + 1
                    });
                });

                order.push({
                    id: parentId,
                    sort: index + 1,
                    children: children
                });
            });
            $('.loadingDiv').show();
            // Send updated order to server
            $.ajax({
                url: "{{ route('admin.widgets.updateSort') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order: order
                },
                success: function (response) {
                    $('.loadingDiv').hide();
                    console.log("Parent order updated successfully.");
                },
                error: function () {
                    alert("An error occurred while updating the parent order.");
                }
            });
        }
    }).disableSelection();

    // Sortable for child widgets
    $(".child-widget").sortable({
        handle: ".drag-handle",
        items: "> li", // Only child items can be dragged
        connectWith: ".child-widget", // Allow connecting only with other child lists
        containment: "parent", // Keep child sorting limited to its parent
        stop: function (event, ui) {
            const parentWidget = $(this).closest(".parent-widget").data("id");
            const childOrder = [];

            $(this).find("> li").each(function (index) {
                childOrder.push({
                    id: $(this).data("id"),
                    sort: index + 1
                });
            });
            $('.loadingDiv').show();
            // Send updated child order to the server
            $.ajax({
                url: "{{ route('admin.widgets.updateSort') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order: [{
                        id: parentWidget,
                        sort: $(this).closest('li').index() + 1,
                        children: childOrder
                    }]
                },
                success: function (response) {
                    $('.loadingDiv').hide();
                    console.log("Child order updated successfully.");
                },
                error: function () {
                    alert("An error occurred while updating the child order.");
                }
            });
        }
    }).disableSelection();

    // Use event delegation for toggle action on child widgets
    $(document).on('click', '.toggle-arrow-widget', function () {
        const parent = $(this).closest("li");
        const childWidget = parent.find(".child-widget");
        childWidget.toggle();
        $(this).text($(this).text() === "▶" ? "▼" : "▶");
    });
});

</script>



@stop
