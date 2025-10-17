@php use App\Models\BillingAddress;  @endphp
<div class="modal-header">
                     <h5 class="modal-title " id="exampleModalLabel">{{ $form_type }} Address</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="shipping-address-area">
                         <div class="shipping-address-form-wrapper">
                             <form action="javascript:;" id="order_address_form" class="shipping-address-form common-form">@csrf
                                 <input type="hidden" name="id" value="{{ $id }}">
                                 <input type="hidden" name="form_type" value="{{ $form_type }}">
								 <div class="row">
                                     <div class="col-lg-4 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label"  >First Name*</label>
                                             <input type="text" name="first_name" id="addr-first_name" @if(!empty($id)) value="{{ $address->first_name }}" @endif    @if(empty($id)) value="{{ Auth::user()->first_name }}" @endif  />
                                         </fieldset>
										  @php echo from_input_error_message('first_name') @endphp
                                     </div>
                                     <div class="col-lg-4 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">Last Name*</label>
                                             <input type="text" name="last_name" id="addr-last_name" @if(!empty($id)) value="{{ $address->last_name }}" @endif  @if(empty($id)) value="{{ Auth::user()->last_name }}" @endif />
                                         </fieldset>
										  @php echo from_input_error_message('last_name') @endphp
                                     </div>
                                     <div class="col-lg-4 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">Email Address*</label>
                                             <input type="email" name="email" id="addr-email" @if(!empty($id)) value="{{ $address->email }}" @endif  @if(empty($id)) value="{{ Auth::user()->email }}" @endif />
                                         </fieldset>
										 @php echo from_input_error_message('email') @endphp
                                     </div>
                                     <div class="col-lg-3 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">Mobile*</label>
                                             <input type="text" id="mobile" name="mobile" id="addr-mobile" @if(!empty($id)) value="{{ $address->mobile }}" @endif  @if(empty($id))  value="{{ Auth::user()->mobile }}" @endif />
											 @php echo from_input_error_message('mobile') @endphp
                                         </fieldset>
                                     </div>
                                     
                                    <div class="col-lg-3 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">Zip Code*</label>
                                             <input type="text" class="pincode" name="postcode" id="addr-postcode" @if(!empty($id)) value="{{ $address->postcode }}" @endif />
											  
                                         </fieldset>
										 @php echo from_input_error_message('postcode') @endphp
                                     </div>
                                    
                                    <div class="col-lg-3 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">State*</label>
                                             <select class="form-select state_list" name="state" id="addr-state">
                                                 <option value="">Select State</option>
													@foreach($states as $state) 
													<option value="{{ $state }}" @if(!empty($id) && $state == $address->state) selected @endif >{{ $state }}</option>                               
													@endforeach
                                             </select>
											 @php echo from_input_error_message('state') @endphp
                                         </fieldset>
                                     </div>
									  <div class="col-lg-3 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">City*</label>
                                             <input type="text" class="city" name="city" id="addr-postcode" @if(!empty($id)) value="{{ $address->city }}" @endif />
											 @php echo from_input_error_message('city') @endphp
                                         </fieldset>
                                     </div>
                                     <div class="col-lg-6 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">Address*</label>
                                             <input type="text" name="address" id="addr-address" @if(!empty($id)) value="{{ $address->address }}" @endif />
                                         </fieldset>
										 @php echo from_input_error_message('address') @endphp
                                     </div>
                                     <div class="col-lg-6 col-md-12 col-12">
                                         <fieldset>
                                             <label class="label">Address 2</label>
                                             <input type="text" name="address_line2" id="addr-address_line2" @if(!empty($id)) value="{{ $address->address_line2 }}" @endif />
                                         </fieldset>
										 @php echo from_input_error_message('address_line2') @endphp
                                     </div>
									 @if($form_type == 'Shipping' && empty(BillingAddress::addresscount()))
									 <div class="col-lg-12 col-md-12 col-12">
											<div class="form-checkbox d-flex align-items-center mt-4">
												<input  type="checkbox" class="w100" name="billing_address_same_as_shipping_address" value="1">
												<label class="form-check-label ms-2">
													Billing address Same as shipping address
												</label>
											</div>
									  </div>
									  @endif
                                 </div>

                             </form>
                         </div>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                     <button type="button" class="btn btn-primary" id="order-address-btn">@if($id == '') Add @else Update @endif{{ $form_type }} Address</button>
                 </div>
				