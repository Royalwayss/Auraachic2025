@extends('front.layout.layout')
@section('content')
<div class="breadcrumb">
            <div class="container">
                <ul class="list-unstyled d-flex align-items-center m-0">
                    <li><a href="/">Home</a></li>
                    <li>
                       <i class="fa-solid fa-angle-right"></i>
                    </li>
                    <li>Register</li>
                </ul>
            </div>
        </div>
        <!-- breadcrumb end -->

        <main id="MainContent" class="content-for-layout">
            <div class="login-page login-page mt-5 mb-5">
                <div class="container">
                    <div class="form-bg">
                        <form action="javascript:;" data-action="{{ route('signup') }}" id="signup-form"  class="login-form common-form mx-auto">@csrf
                        <div class="section-header">
                            <h2 class="section-heading">Register</h2>
                        </div>
                        <div class="row">
                            
							 
                            <div class="col-12 otp">
                                <fieldset>
                                    <label class="label">Email</label>
                                    <div class="d-flex">
                                    <input type="email" name="email" id="signup-email" class="w-80"/>
                                     <button type="submit" id="sendOtpBtn" class="btn-signin w-20" data-action="send_otp" >Send OTP</button>
                                     </div>
									 <p  id="msg-signup-email"></p>
                                </fieldset>
                               
                            </div> 
							<small  id="countdown" class="text-muted"></small>
							<div id="otp-fields" style="display:none">
							<div class="col-12">
                                <fieldset>
                                    <label class="label">Otp</label>
                                    <div class="d-flex">
                                        <input type="email" name="otp" id="signup-otp" />
                                     </div>
									 <p  id="msg-signup-otp"></p>
                                </fieldset>
                               
                            </div>
							<input type="hidden" name="action" id="signup-action">
							<div class="col-12">
                                <fieldset>
                                    <label class="label">Mobile</label>
                                    <input type="number" name="mobile" name="mobile" id="signup-mobile" />
                                </fieldset>
								<p  id="msg-signup-mobile"></p>
                            </div>
							
                            <div class="col-12 mt-3">
                                <button type="submit" class="d-block mt-4 btn-signin">SIGN UP</button>
                                <a href="{{ route('signin') }}" class="btn-secondary mt-2 btn-register">Already have a account? <span style="color:#d3b696; padding-left:5px;"> Login</span></a>
                            </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>            
        </main>
@endsection