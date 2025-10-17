@php $selProductIds = array(); 
    $selCategoryIds = array();  
    $selBrandIds    = array(); 
    $desktopImage   = ""; 
    $mobileImage    = ""; 
    $video          = ""; 
@endphp
@if(isset($widget['widget_content']))
    @php 
        $selProductIds = array_filter(array_column($widget['widget_content'],'product_id'));
        $selCategoryIds = array_filter(array_column($widget['widget_content'],'category_id'));
        $selBrandIds = array_filter(array_column($widget['widget_content'],'brand_id'));
    @endphp
@endif


@if(!isset($widget['id']) && $widgetType == "MULTIPLE_BANNERS")
    <div class="form-group">
        <label class="col-md-3 control-label">Select Parent (if any) <span class="asteric">*</span></label>
        <div class="col-md-4">
            <select class="form-control" name="parent_id" required>
                <option value="">Please Select</option>
                <option value="new">Create New Section</option>
                @foreach($getMultipleBannerParents as $multipleBanner)
                    <option value="{{$multipleBanner['id']}}">Add Under ({{$multipleBanner['heading']}})</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Banner Common Content <span class="asteric">*</span></label>
        <div class="col-md-4">
            <textarea  type="text" placeholder="Enter Content..." name="content" style="color:gray" class="form-control">{{(!empty($widget['content']))?$widget['content']: '' }}</textarea>
        </div>
    </div>
    
    <span id="SectionType"></span>
@endif

@if($widgetType == "SINGLE_BANNER" || $widgetType == "SINGLE_DESCRIPTIVE_BANNER" || $widgetType == "SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS" || $widgetType == "MULTIPLE_BANNERS")
    @if(isset($widget['widget_content']) && !empty($widget['widget_content']))
        @foreach($widget['widget_content'] as $content)
            @if(!empty($content['desktop_image']))
                <?php 
                    $desktopImage = $content['desktop_image_url'];
                    $mobileImage = $content['mobile_image_url'];
                    break;
                ?>
            @endif
        @endforeach
    @endif
    <div class="form-group">
        <label class="col-md-3 control-label">Desktop Image <span class="asteric">*</span></label>
        <div class="col-md-4">
            <input class="form-control" type="file" name="desktop_image" accept="image/*">
            @if(!empty($desktopImage))
                <br>
                <a target="_blank" href="{{$desktopImage}}"><img width="150px" src="{{$desktopImage}}"></a>
            @endif
        </div>
    </div>
    <div class="form-group">
        <label class="col-md-3 control-label">Mobile Image <span class="asteric">*</span></label>
        <div class="col-md-4">
            <input type="file" class="form-control" name="mobile_image" accept="image/*">
            @if(!empty($mobileImage))
                <br>
                <a target="_blank" href="{{$mobileImage}}"><img width="150px" src="{{$mobileImage}}"></a>
            @endif
        </div>
    </div>
@endif
@if($widgetType == "SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS")
    @if(isset($widget['widget_content']) && !empty($widget['widget_content']))
        @foreach($widget['widget_content'] as $content)
            @if(!empty($content['video']))
                <?php 
                    $video = $content['video_url'];
                    break;
                ?>
            @endif
        @endforeach
    @endif
    <div class="form-group">
        <label class="col-md-3 control-label">Video <span class="asteric">*</span></label>
        <div class="col-md-4">
            <input class="form-control" type="file" name="video">
            @if(!empty($video))
                <br>
                <a target="_blank" href="{{$video}}">View Video</a>
            @endif
        </div>
    </div>
@endif
@if($widgetType == "SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS" || $widgetType == "SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS" || $widgetType == "MULTIPLE_PRODUCTS")
    <div class="form-group">
        <label class="col-md-3 control-label">Select Products <span class="asteric">*</span></label>
        <div class="col-md-9">
            <select name="products[]" class="select2" multiple>
                @foreach ($products as $key => $product) 
                    <option value="{{$product['id']}}" @if(in_array($product['id'],$selProductIds)) selected @endif>{{$product['product_name']}} ({{$product['product_code']}})</option>
                @endforeach
            </select>
        </div>
    </div>
@endif
@if($widgetType == "MULTIPLE_CATEGORIES")
    <div class="form-group">
        <label class="col-md-3 control-label">Select Categories <span class="asteric">*</span></label>
        <div class="col-md-9">
            <select name="categories[]" class="select2" multiple>
                @foreach ($categories as $key => $category) 
                    <option value="{{$category['id']}}" @if(in_array($category['id'],$selCategoryIds)) selected @endif>{{$category['category_name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif
@if($widgetType =="PRODUCTS_FROM_CATEGORIES")
    <div class="form-group">
        <label class="col-md-3 control-label">Select Categories <span class="asteric">*</span></label>
        <div class="col-md-9">
            <select name="categories[]" class="select2" multiple>
                @foreach ($allCategories as $key => $category)
                    <option value="{{$category['id']}}" @if(in_array($category['id'],$selCategoryIds)) selected @endif>&#9679;&nbsp;{{$category['category_name']}}</option>
                    @foreach ($category['subcategories'] as $key => $subcat)
                        <option value="{{$subcat['id']}}" @if(in_array($subcat['id'],$selCategoryIds)) selected @endif>&nbsp;&nbsp;&nbsp;&nbsp;&raquo; &nbsp;{{$subcat['category_name']}}</option>
                        @foreach ($subcat['subcategories'] as $key => $subsubcat)
                            <option value="{{$subsubcat['id']}}" @if(in_array($subsubcat['id'],$selCategoryIds)) selected @endif>&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&raquo; &raquo; &nbsp;{{$subsubcat['category_name']}}</option>
                        @endforeach
                    @endforeach
                @endforeach
            </select>
            @if($widgetType =="PRODUCTS_FROM_CATEGORIES")
                Note:- It will pick any 10 random products from above selected categories
            @endif
        </div>
    </div>
@endif
@if($widgetType == "MULTIPLE_BRANDS")
    <div class="form-group">
        <label class="col-md-3 control-label">Select Brands <span class="asteric">*</span></label>
        <div class="col-md-9">
            <select name="brands[]" class="select2" multiple>
                @foreach ($brands as $key => $brand) 
                    <option value="{{$brand['id']}}" @if(in_array($brand['id'],$selBrandIds)) selected @endif>{{$brand['brand_name']}}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif

@if($widgetType == "TAB_WISE_PRODUCTS")
    <span id="AppendTabProducts">
        <hr>
        @if(isset($widget['child_widgets']) && !empty($widget['child_widgets']))
            @foreach($widget['child_widgets'] as $key => $childWidget)
                @php $i = ++$key; 
                    $selProductIds = array_filter(array_column($childWidget['widget_content'],'product_id'));
                @endphp
                <div class="form-group">
                    <label class="col-md-3 control-label">Tab {{$i}} Title <span class="asteric">*</span></label>
                    <div class="col-md-4">
                        <input type="text" name="tab_title[{{$i}}]" class="form-control" placeholder="Enter Title" value="{{$childWidget['heading']}}">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Tab {{$i}} Products <span class="asteric">*</span></label>
                    <div class="col-md-8">
                        <select name="tab_products[{{$i}}][]" class="select2" multiple required>
                            @foreach (products() as $key => $product) 
                                <option value="{{$product['id']}}" @if(in_array($product['id'],$selProductIds)) selected @endif>{{$product['product_name']}} ({{$product['product_code']}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr>
            @endforeach
        @else
            @for($i=1; $i<=1;$i++)
                <div class="form-group">
                    <label class="col-md-3 control-label">Tab {{$i}} Title <span class="asteric">*</span></label>
                    <div class="col-md-4">
                        <input type="text" name="tab_title[{{$i}}]" class="form-control" placeholder="Enter Title">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-md-3 control-label">Tab {{$i}} Products <span class="asteric">*</span></label>
                    <div class="col-md-8">
                        <select name="tab_products[{{$i}}][]" class="select2" multiple required>
                            @foreach (products() as $key => $product) 
                                <option value="{{$product['id']}}">{{$product['product_name']}} ({{$product['product_code']}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <hr>
            @endfor
        @endif
    </span>
    <div class="form-group">
        <label class="col-md-3 control-label">
            <button type="button" id="AddMoreTabs">Add More</button>
        </label>
    </div>
@endif

@if($widgetType == "THIRD_PARTY_SCRIPTS")
    <div class="form-group">
        <label class="col-md-3 control-label">Enter Script <span class="asteric">*</span></label>
        <div class="col-md-9">
            <textarea class="form-control" placeholder="Enter Script Code" name="script_code" rows="15">{{(!empty($widget['script_code']))?$widget['script_code']: '' }}</textarea>
            <p>Note:- Please always verify the scripts from <a target="_blank" href="https://jsfiddle.net/">Jsfiddle</a></p>
        </div>
    </div>
@endif
@if($widgetType == "SINGLE_BANNER" || $widgetType == "SINGLE_DESCRIPTIVE_BANNER" || $widgetType == "MULTIPLE_BANNERS" || $widgetType == "SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS" || $widgetType == "MULTIPLE_PRODUCTS" || $widgetType == "PRODUCTS_FROM_CATEGORIES")
<!-- Banners Redirection -->
    <div class="form-group">
        <label class="col-md-3 control-label">[Banner / View All] Link To <span class="asteric">*</span></label>
        <div class="col-md-4">
            <select class="form-control" name="link_to">
                <option value="">No Link</option>
                @foreach(widgetLinkTypes() as $linkType)
                    <option value="{{$linkType}}" @if(isset($widget['link_to'])  && $widget['link_to'] == $linkType) selected @endif>{{$linkType}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <span id="AppendRedirections">
        
    </span>

    @if($widgetType == "SINGLE_BANNER" || $widgetType == "SINGLE_DESCRIPTIVE_BANNER" || $widgetType == "MULTIPLE_BANNERS" || $widgetType == "SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS")
        <div class="form-group">
            <label class="col-md-3 control-label">Alt Tag <span class="asteric">*</span></label>
            <div class="col-md-4">
                <input  type="text" placeholder="Alt Tag" name="alt_tag" style="color:gray" class="form-control" value="{{(!empty($widget['alt_tag']))?$widget['alt_tag']: '' }}"/>
            </div>
        </div>
        <div class="form-group">
            <label class="col-md-3 control-label">Title Tag <span class="asteric">*</span></label>
            <div class="col-md-4">
                <input  type="text" placeholder="Title Tag" name="title_tag" style="color:gray" class="form-control" value="{{(!empty($widget['title_tag']))?$widget['title_tag']: '' }}"/>
            </div>
        </div>
    @endif
@endif