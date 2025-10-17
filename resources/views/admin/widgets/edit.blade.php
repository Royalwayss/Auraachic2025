@extends('admin.layout.layout')
@section('content')
@include('admin.widgets.partials.form')
<script src="{{ asset('admin/plugins/jquery/jquery.min.js') }}"></script>
<script type="text/javascript">
    var ajaxUrl = '{{ route("widgets.update", [$widget['id']])  }}';
</script>
<script type="text/javascript">
    $("#WidgetForm").submit(function(e){
        e.preventDefault();
        var formdata = new FormData(this);
        ajaxFormRequest(ajaxUrl,'POST',formdata);
    });
</script>
@include('admin.widgets.partials.scripts')
@endsection