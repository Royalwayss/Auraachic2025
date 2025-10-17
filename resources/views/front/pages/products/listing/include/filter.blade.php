@php
use App\Models\Category;
use App\Models\ProductsAttribute;
use App\Models\ProductsFilter;
$categories = Category::getCategories($type='Front');  
$sizes = ProductsAttribute::sizes($catids,$catseo);
$sizes = json_decode(json_encode($sizes),true);
$sizes = array_combine($sizes, $sizes);
$colors = ProductsFilter::selfilters('family_color',$catids,$catseo); 
$fits = ProductsFilter::selfilters('fit',$catids,$catseo); 
$necks = ProductsFilter::selfilters('neck',$catids,$catseo); 
$fabrics = ProductsFilter::selfilters('fabric',$catids,$catseo); 
$occasions = ProductsFilter::selfilters('occasion',$catids,$catseo); 
$sleeves = ProductsFilter::selfilters('sleeve',$catids,$catseo); 
@endphp
<!-- sidebar start -->
<div class="col-lg-2 col-md-12 col-12">
   <div class="collection-filter filter-drawer">
      <div class="filter-widget d-lg-none d-flex align-items-center justify-content-between">
         <h5 class="heading_24">
         Filter By</h4>
         <button type="button" class="btn-close text-reset filter-drawer-trigger d-lg-none absClear"></button>
      </div>
      <div class="sidebar">
         <div class="collection-title-wrap ">
            <h2 class="collection-title heading_24 mb-5">Filters</h2>
            <button class="absClear">Clear All</button>
         </div>
         <div class="filter-widget">
            <div class="filter-header faq-heading heading_16 d-flex align-items-center justify-content-between border-bottom collapsed"
               data-bs-toggle="collapse" data-bs-target="#filter-collection">
               Categories
               <span class="faq-heading-icon">
               <i class="fa-solid fa-chevron-down"></i>
               </span>
            </div>
            <div id="filter-collection" class="accordion-collapse collapse">
               <ul class="filter-lists list-unstyled mb-0">
                  @foreach($categories as $category)
                  <li class="filter-item">
                     <label class="filter-label" onclick="window.location.href='{{ url($category['url']) }}'">
                     <input type="checkbox" />
                     <span class="filter-checkbox rounded me-2"></span>
                     <span class="filter-text">{{  $category['category_name'] }}</span>
                     </label>
                  </li>
                  @foreach($category['subcategories'] as $subcategory)
                  <li class="filter-item ml-10">
                     <label class="filter-label" onclick="window.location.href='{{ url($subcategory['url']) }}'">
                     <input type="checkbox" />
                     <span class="filter-checkbox rounded me-2"></span>
                     <span class="filter-text">{{  $subcategory['category_name'] }}</span>
                     </label>
                  </li>
                  @endforeach
                  @endforeach
               </ul>
            </div>
         </div>
         @if(!empty($fabrics))
         <div class="filter-widget">
            <div class="filter-header faq-heading heading_16 d-flex align-items-center justify-content-between border-bottom collapsed"
               data-bs-toggle="collapse" data-bs-target="#filter-fabric">
               Fabric
               <span class="faq-heading-icon">
               <i class="fa-solid fa-chevron-down"></i>
               </span>
            </div>
            <div id="filter-fabric" class="accordion-collapse collapse">
               <ul class="filter-lists list-unstyled mb-0">
                  @foreach($fabrics as $fabric)
                  <?php 
                        if(isset($_GET['fabric']) && !empty($_GET['fabric'])){
                            $explodefabricArr = explode('~',$_GET['fabric']);
							if(!empty($explodefabricArr) && in_array(str_replace('/','_',$fabric), $explodefabricArr)){
							   $fabricchecked  ="checked";
							}else{
							   $fabricchecked  ="";
							}
                        }else{
                           $fabricchecked  ="";
                        } 
                     ?>
                  <li class="filter-item">
                     <label class="filter-label">
                     <input type="checkbox" name="fabric" value="{{ $fabric }}" class="filterAjax" {{ $fabricchecked }} />
                     <span class="filter-checkbox rounded me-2"></span>
                     <span class="filter-text">{{ $fabric }}</span>
                     </label>
                  </li>
                  @endforeach
               </ul>
            </div>
         </div>
         @endif
		 @if(!empty($necks))
	       <div class="filter-widget">
            <div class="filter-header faq-heading heading_16 d-flex align-items-center justify-content-between border-bottom collapsed"
               data-bs-toggle="collapse" data-bs-target="#filter-design">
               Neck Design
               <span class="faq-heading-icon">
                         <i class="fa-solid fa-chevron-down"></i>
               </span>
            </div>
            <div id="filter-design" class="accordion-collapse collapse">
               <ul class="filter-lists list-unstyled mb-0">
                  @foreach($necks as $neck)
				  <?php 
                        if(isset($_GET['neck']) && !empty($_GET['neck'])){
                            $explodeneckArr = explode('~',$_GET['neck']);
							if(!empty($explodeneckArr) && in_array(str_replace('/','_',$neck), $explodeneckArr)){
							   $neckchecked  ="checked";
							}else{
							   $neckchecked  ="";
							}
                        }else{
                           $neckchecked  ="";
                        } 
                  ?>
				  <li class="filter-item">
                     <label class="filter-label">
                     <input type="checkbox" name="neck" value="{{ $neck }}" class="filterAjax" {{ $neckchecked }}/>
                     <span class="filter-checkbox rounded me-2"></span>
                     <span class="filter-text">{{ $neck }}</span>
                     </label>
                  </li>
				  @endforeach
               </ul>
            </div>
         </div>
		 @endif
         <div class="filter-widget">
            <div class="filter-header faq-heading heading_16 d-flex align-items-center justify-content-between border-bottom collapsed"
               data-bs-toggle="collapse" data-bs-target="#filter-price">
               Price
               <span class="faq-heading-icon">
               <i class="fa-solid fa-chevron-down"></i>
               </span>
            </div>
            <div id="filter-price" class="accordion-collapse collapse">
               <div class="filter-price d-flex align-items-center justify-content-between">
                  <div class="filter-field">
                     <input class="field-input price_rage" id="price_from" type="number" value="500">
                  </div>
                  <div class="filter-separator px-1">To</div>
                  <div class="filter-field">
                     <input class="field-input price_rage" id="price_to" type="number"  value="1000"> 
                  </div>
               </div>
			   <div class="filter-price filter-price-apply d-flex align-items-center justify-content-between text-center">
			     <button id="ApplyPrice" @if(isset($_GET['price']) && !empty($_GET['price'])) class="price-filter-active rounded" @else class="rounded" @endif >Apply Price Filter</button>
			   </div>
			   
            </div>
         </div>
		 <input type="checkbox" id="filter_price" class="filterAjax" name="price" value="500-1000" style="display:none">
         <div class="filter-widget filter-color">
            <div class="filter-header faq-heading heading_16 d-flex align-items-center justify-content-between border-bottom collapsed"
               data-bs-toggle="collapse" data-bs-target="#filter-color">
               Colors
               <span class="faq-heading-icon">
               <i class="fa-solid fa-chevron-down"></i>
               </span>
            </div>
            <div id="filter-color" class="accordion-collapse collapse">
               <ul class="filter-lists list-unstyled mb-0">
                  @foreach($colors as $color)
                  <?php 
                        if(isset($_GET['color']) && !empty($_GET['color'])){
                            $explodecolorArr = explode('~',$_GET['color']);
							if(!empty($explodecolorArr) && in_array(str_replace('/','_',$color), $explodecolorArr)){
							   $colorchecked  ="checked";
							}else{
							   $colorchecked  ="";
							}
                        }else{
                           $colorchecked  ="";
                        } 
                  ?>
				  <li class="filter-item">
                     <label class="filter-label " style="background-color:{{ $color }}">
                     <input type="checkbox" name="color" value="{{ $color }}" class="filterAjax" {{ $colorchecked }} />
                     <span class="filter-checkbox rounded me-2"></span>
                     </label>
                  </li>
                  @endforeach
               </ul>
            </div>
         </div>
         <div class="filter-widget">
            <div class="filter-header faq-heading heading_16 d-flex align-items-center justify-content-between border-bottom collapsed"
               data-bs-toggle="collapse" data-bs-target="#filter-size">
               Size
               <span class="faq-heading-icon">
               <i class="fa-solid fa-chevron-down"></i>
            </div>
            <div id="filter-size" class="accordion-collapse collapse">
               <ul class="filter-lists list-unstyled mb-0">
                  @foreach($sizes as $size)
                  <?php 
                        if(isset($_GET['size']) && !empty($_GET['size'])){
                            $explodesizeArr = explode('~',$_GET['size']);
							if(!empty($explodesizeArr) && in_array(str_replace('/','_',$size), $explodesizeArr)){
							   $sizechecked  ="checked";
							}else{
							   $sizechecked  ="";
							}
                        }else{
                           $sizechecked  ="";
                        } 
                     ?>
				  <li class="filter-item">
                     <label class="filter-label">
                     <input type="checkbox" name="size" value="{{ $size }}" class="filterAjax" {{ $sizechecked }}/>
                     <span class="filter-checkbox rounded me-2"></span>
                     <span class="filter-text">{{ $size }}</span>
                     </label>
                  </li>
                  @endforeach
               </ul>
            </div>
         </div>
		 
		  <div class="d-none mob-apply-filter d-flex align-items-center justify-content-between text-center">
		           <button class="rounded filter-drawer-trigger">Apply All Filter</button>
		  </div>
		 
		 
		 
      </div>
   </div>
</div>
<!-- sidebar end -->
</div>