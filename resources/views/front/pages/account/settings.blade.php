@extends('front.layout.layout')
@section('content')
<div class="breadcrumb">
    <div class="container">
        <ul class="list-unstyled d-flex align-items-center m-0">
            <li><a href="/">Home</a></li>
            <li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
            <li><a href="{{ route('account',['profile']) }}">Account</a></li>
			<li><svg class="svg-inline--fa fa-angle-right" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="angle-right" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" data-fa-i2svg=""><path fill="currentColor" d="M278.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-160 160c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L210.7 256 73.4 118.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l160 160z"></path></svg><!-- <i class="fa-solid fa-angle-right"></i> Font Awesome fontawesome.com --></li>
			<li>Change Password</li>
        </ul>
    </div>
</div>

<main id="MainContent" class="content-for-layout">
    <section class="collection listing">
        <div class="accWrap container">
            <div class="row">
                @include('front.pages.account.account_tabs')
                <div class="col-lg-9 col-md-12 address-detail-area">
                    <h3>Change Password</h3>
                    <hr>
                    <div class="accPage">
                          <form id="SettingsForm" action="javascript:;" data-action="{{ route('changepassword') }}">@csrf
						  <div class="row">
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">Current Password</label><br>
                                <input type="text"  class="w-100" name="current_password" placeholder="Enter Current Password*">
                                @php echo from_input_error_message('current_password') @endphp
							</div>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">New Password</label><br>
                                <input type="text"  class="w-100" name="new_password" placeholder="Enter Current Password*">
								@php echo from_input_error_message('new_password') @endphp
                            </div>
                            <div class="col-lg-6 col-md-12 mb-3">
                                <label for="">Confirm Password</label><br>
                                <input type="text" class="w-100" name="confirm_password" placeholder="Enter Confirm Password*">
								@php echo from_input_error_message('confirm_password') @endphp
                            </div>
                            
                            <div class="col-12">
                            <button type="submit" class="icButton button2">Update</button>
                            </div>
                        </div>
						</form>
                    </div>
                </div>

            </div>
        </div>
        <!-- Edit Address Modal -->
    </section>
</main>
 <script src="{{ asset('front/assets/js/ajax_jquery.min.js') }}"></script>
<script>
$(document).ready(function ($)  {
 $("#SettingsForm").submit(function (e) {
        e.preventDefault();
        $('.loadingDiv').show();
        var formdata = $("#SettingsForm").serialize();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: $("#SettingsForm").attr('data-action'),
            type: 'POST',
            data: formdata,
            success: function (data) {
                $('.loadingDiv').hide();
                $('.error_message').empty();
				if (!data.status) {
                    $.each(data.errors, function (i, error) {
                        $('#input-error-' + i).css({'color': 'red' });
						$('#input-error-' + i).html(error);
                    });
                } else {
                     $("#SettingsForm").trigger("reset");
					 notifyMessage(data.message,'success'); 
                }
            },
			error: function () {
				alert('Error');
                $('.loadingDiv').hide();
            }
        });
    });
    });
</script>
@endsection