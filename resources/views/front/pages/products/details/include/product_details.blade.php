<!-- product tab start -->
    <div class="container product-tab-section p-5" data-aos="fade-up" data-aos-duration="700">
        <div class="container">
            <div class="tab-list product-tab-list">
                <nav class="nav product-tab-nav">
                  
				    <a class="product-tab-link tab-link active" href="#pdescription" data-bs-toggle="tab">Product Detail</a>
                  
				     @if(!empty(trim($productdetails['wash_care'])))
				     <a class="product-tab-link tab-link" href="#pshipping" data-bs-toggle="tab">Wash care instructions</a>
                     @endif
					 @if(!empty(trim($productdetails['key_features'])))
					<a class="product-tab-link tab-link" href="#pstyle" data-bs-toggle="tab">Why this style </a>
                     @endif
					<a class="product-tab-link tab-link" href="#preview" data-bs-toggle="tab">Reviews</a>
                    <a class="product-tab-link tab-link" href="#custom" data-bs-toggle="tab">Need a custom fit </a>
                </nav>
            </div>
            <div class="tab-content product-tab-content">
                <div id="pdescription" class="tab-pane fade show active">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-12">
                            <div class="desc-content">
                                    @if(!empty(trim($productdetails['description'])))
									@php echo $productdetails['description'] @endphp 
                                    @endif
                                    <ul class="detailsNavs mt-10">
								<!-- <li><strong>Key Features</strong> <br>Onvers basic round neck sando t-shirt sports fit </li> -->
								    <li><strong>Product Color</strong> <br>{{ $productdetails['family_color'] }}</li>
                                    @if(!empty(trim($productdetails['description'])))
									<li><strong>Fabric</strong> <br>{{ $productdetails['family_color'] }}</li>
                                    @endif
									@if(!empty(trim($productdetails['sleeve'])))
									<li><strong>Sleeve</strong> <br>{{ $productdetails['sleeve'] }}</li>
								    @endif
                                    @if(!empty(trim($productdetails['neck'])))
									<li><strong>Neck</strong> <br>{{ $productdetails['neck'] }}</li>
								    @endif
                                    @if(!empty(trim($productdetails['fit'])))
									<li><strong>Fit</strong> <br>{{ $productdetails['fit'] }}</li>
                                    @endif
									@if(!empty(trim($productdetails['occasion'])))
								    <li><strong>Occasion</strong> <br>{{ $productdetails['occasion'] }}</li>
                                    @endif
									@if(!empty(trim($productdetails['product_code'])))
									<li><strong>Product Code</strong> <br>{{ $productdetails['product_code'] }}</li>	
									@endif								
							    </ul>
                            </div>
                        </div>
                    </div>
                </div>
				
                <div id="pshipping" class="tab-pane fade">
                    <div class="desc-content">
                        @php echo $productdetails['wash_care'] @endphp 
                    </div>
                </div>
				
				 @if(!empty(trim($productdetails['key_features'])))
                <div id="pstyle" class="tab-pane fade">
                    <div class="desc-content">
                        @php echo $productdetails['key_features'] @endphp  
                    </div>
                </div>
				@endif
                <div id="preview" class="tab-pane fade">
                    <div class="review-area accordion-parent">
                        <h4 class="heading_18 mb-20">Customer Reviews</h4>
                        <div class="review-header d-flex justify-content-between align-items-center">
								@if( count($reviews) )
									<div class="revList">
									@foreach($reviews as $review)
										<div class="reviewDtlWrap">
											<div class="userRev">
												<div class="user">
													<div class="nameInfo">
														<h6>{{ ucwords($review['title']) }}</h6>
														<div class="starWrap">
															@for($i=0; $i<$review['rating']; $i++)
															<img src="{{ asset('front/assets/img/icon/star.png') }}" alt="starIcon">
															@endfor
														</div>
													</div>
												</div>
												<p>{{ $review['review'] }}</p>
											</div>
										</div>
									@endforeach
									</div>
								@else
									<p class="text_16">No reviews yet.</p>
								@endif
							
							
							
                            
							
							<button class="text_14 bg-transparent text-decoration-underline write-btn"
                                type="button" @if(!Auth::check()) onclick="window.location.href='{{ route('signin') }}'" @endif >Write a review</button>
                        </div>
                        @if(Auth::check())
						<div class="review-form-area accordion-child">
                            <form action="javascript:;" data-action="{{ route('savereview') }}" id="review-form">@csrf
							<input type="hidden" name="product_id" value="{{ $productdetails['id'] }}">
                                <fieldset>
                                    <label class="label">Rating*</label>
                                    <div class="star-rating" id="rating">
                                        <fieldset class="rating">
                                            @for($i=5;$i>0;$i--)
											<input type="radio" id="star{{$i}}" name="rating" value="{{$i}}" @if($i == 5) checked @endif /><label class="full" for="star{{$i}}"></label>
                                            @endfor
										</fieldset>
                                    </div>
									 @php echo from_input_error_message('rating') @endphp
                                </fieldset>
                                <fieldset>
                                    <label class="label">Review Title*</label>
                                    <input type="text" name="review_title" placeholder="Give your review a title" />
									
                                </fieldset>
								@php echo from_input_error_message('review_title') @endphp 
                                <fieldset>
                                    <label class="label" id="charCountDisplay">Body of Review (2000)*</label>
                                    <textarea cols="30" rows="5" name="review" id="product_review_message" placeholder="Write your comments here" maxlength="2000"></textarea>
                                    @php echo from_input_error_message('review') @endphp
								</fieldset>

                                <button type="submit" id="review-submit" class="position-relative review-submit-btn">SUBMIT</button>
                            </form>
                        </div>
						@endif
                    </div>
                </div>
                <div id="custom" class="tab-pane fade">
                    <div class="custom-area accordion-parent col-lg-8 col-12">
                        <h4 class="heading_18 mb-2">Need a custom fit</h4>
                         <form action="javascript:;" data-action="{{ route('savecustomfit') }}" id="custom-fit-form">
                                <input type="hidden" name="product_id" value="{{ $productdetails['id'] }}">
								<fieldset>
                                    <input type="text" name="title" placeholder="Title*" />
									@php echo from_input_error_message('title') @endphp
                                </fieldset>
                                <fieldset>
                                    <input type="text" name="mobile" placeholder="Mobile*" />
									@php echo from_input_error_message('mobile') @endphp
                                </fieldset>
                                <fieldset>
                                    <textarea cols="30" name="message" rows="5" placeholder="Write a message*"></textarea>
									@php echo from_input_error_message('message') @endphp
                                </fieldset>

                                <button type="submit" id="custom-fit-submit" class="review-submit-btn">SUBMIT</button>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- product tab end -->