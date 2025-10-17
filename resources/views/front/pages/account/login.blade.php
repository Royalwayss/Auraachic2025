@extends('front.layout.layout')
@section('content')
<div class="breadcrumb">
   <div class="container">
      <ul class="list-unstyled d-flex align-items-center m-0">
         <li><a href="/">Home</a></li>
         <li>
            <i class="fa-solid fa-angle-right"></i>
         </li>
         <li>Login</li>
      </ul>
   </div>
</div>
<!-- breadcrumb end -->
<main id="MainContent" class="content-for-layout">
   <div class="login-page login-page mt-5 mb-5">
      <div class="container">
         <div class="form-bg">
            <form action="javascript:;" data-action="{{ route('signin') }}" id="login-form" class="login-form common-form mx-auto">
               @csrf
               <div class="section-header">
                  <h2 class="section-heading">Login</h2>
               </div>
               <div class="row">
                  <div id="alert-danger" style="color:red"></div>
                  <div class="col-12 otp">
                     <fieldset>
                        <label class="label">Login With</label>
                        <div class="d-flex">
                           <select id="login_type" name="login_type" class="w-80">
                              <option value="otp">Email Otp</option>
                              <option value="password">Password</option>
                           </select>
                        </div>
                     </fieldset>
                  </div>
                  <div class="col-12 otp">
                     <fieldset>
                        <label class="label">Email id </label>
                        <div class="d-flex">
                           <input type="text" name="email" id="login-email" class="w-80" autocomplete="new-password"/>
                           <button type="submit"  class="login-otp-btn login-btn w-20" id="sendOtpBtn" data-action="sent_otp">Send OTP</button>
                        </div>
                        <p id="msg-login-email"></p>
                     </fieldset>
                     <small  id="countdown" class="text-muted"></small>
                  </div>
                  <div class="col-12 login-password" style="display:none">
                     <fieldset>
                        <label class="label">Password</label>
                        <input type="password" name="password" id="login-password" autocomplete="new-password"/>
                     </fieldset>
                     <p  id="msg-login-password"></p>
                  </div>
                  <div class="col-12 otp-field" style="display:none">
                     <fieldset>
                        <label class="label">Otp</label>
                        <input type="number" name="otp" id="login-otp" />
                     </fieldset>
                     <p  id="msg-login-otp"></p>
                  </div>
                  <p id="login-error"></p>
                  <input type="hidden" name="action" id="login-action">
                  <div class="col-12 mt-3">
                     <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#ForgotModal" class="text_14 d-block">Forgot your password?</a>
                     <button type="submit" class="d-block mt-4 login-btn btn-signin" data-action="">SIGN IN</button>
                     <a href="{{ route('signup') }}" class="btn-secondary mt-2 btn-register">CREATE AN ACCOUNT</a>
                  </div>
               </div>
            </form>
         </div>
      </div>
   </div>
</main>
<!-- forgot password-->

    <div class="modal fade" id="ForgotModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Forgot Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="javascript:;" data-action="{{ route('forgotpassword') }}" id="forgot-password-form" class="forgot-form">@csrf
                        <p id="forgotpassword-alert-message"></p>
						<div class="log-email">
                            <label class="d-block pb-2">Email id<span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="forgot-password-email" required="">
                            </div>
							 <p  id="msg-forgotpassword-email"></p>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="submit" class=" m-0 button2 forgot-password-btn">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection