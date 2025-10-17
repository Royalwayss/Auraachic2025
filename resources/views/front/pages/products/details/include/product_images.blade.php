<div class="col-lg-6 col-md-12 col-12">
                    <div class="product-gallery product-gallery-vertical d-flex">
                        <div class="product-img-large">
                            <div class="img-large-slider common-slider" data-slick='{
                                        "slidesToShow": 1, 
                                        "slidesToScroll": 1,
                                        "dots": false,
                                        "arrows": false,
                                        "asNavFor": ".img-thumb-slider"
                                    }'>
                                
								@foreach($productdetails['productimages'] as $productimage)
								<div class="img-large-wrapper">
                                    <a href="{{ asset('front/images/products/large/'.$productimage['image']) }}" data-fancybox="gallery">
                                        <img src="{{ asset('front/images/products/large/'.$productimage['image']) }}" alt="img">
                                    </a>
                                </div>
                                @endforeach
                              
                               
                               
                                @if($productdetails['video_thumbnail'] != '')
                                <div class="img-large-wrapper video-thumb">
                                   <a data-fancybox href="#"
                                        data-type="html"
                                        data-src='<video controls autoplay muted playsinline style="width:100%;"><source src="{{ asset("front/videos/products/".$productdetails["product_video"]) }}" type="video/mp4"></video>'>
                                        <i class="fa-solid fa-circle-play"></i>
                                        <img src="{{ asset('front/videos/thumbnails/'.$productdetails['video_thumbnail']) }}" alt="Click to Play" />
                                        </a>
                                </div>
								@endif
                            </div>
                        </div>
                        <div class="product-img-thumb">
                            <div class="img-thumb-slider common-slider" data-vertical-slider="true" data-slick='{
                                        "slidesToShow": 5, 
                                        "slidesToScroll": 1,
                                        "dots": false,
                                        "arrows": true,
                                        "infinite": false,
                                        "speed": 300,
                                        "cssEase": "ease",
                                        "focusOnSelect": true,
                                        "swipeToSlide": true,
                                        "asNavFor": ".img-large-slider"
                                    }'>
									
							@foreach($productdetails['productimages'] as $productimage)		
                                <div>
                                    <div class="img-thumb-wrapper">
                                        <img src="{{ asset('front/images/products/large/'.$productimage['image']) }}" alt="img">
                                    </div>
                                </div>
                            @endforeach  
                           @if($productdetails['video_thumbnail'] != '')   
                                <div>
                                    <div class="img-thumb-wrapper video-thumbnail">
                                        <i class="fa-solid fa-circle-play"></i>
                                        <img src="{{ asset('front/videos/thumbnails/'.$productdetails['video_thumbnail']) }}" alt="img">
                                    </div>
                                </div>
                            @endif   
                               
                            </div>
                            <div
                                class="activate-arrows show-arrows-always arrows-white d-none d-lg-flex justify-content-between mt-3">
                            </div>
                        </div>
                    </div>
                </div>