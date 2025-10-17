<!-- product order address start -->
<div class="modal fade" id="OrderAddressModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
         <div class="modal-dialog modal-xl">
             <div class="modal-content" id="OrderAddressModalContent">
               
             </div>
         </div>
</div>
<!-- product order address end -->

<!-- product quickview start -->
<div class="modal fade" tabindex="-1" id="quickview-modal">
   <div class="modal-dialog modal-dialog-centered modal-xl">
      <div class="modal-content">
         <div class="modal-header border-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
         </div>
         <div class="modal-body pb-5" id="ProductQuickViewModalContent">
           
		 </div>
      </div>
   </div>
</div>
<!-- product quickview end -->





     <!--------logout----->

     <div class="modal fade show" id="logoutModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-modal="true"
         role="dialog">
         <div class="modal-dialog modal-dialog-centered">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title">Do you want to Logout ?</h5>
                     <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                 </div>
                 <div class="modal-body">
                     <div class="logWrap">
                         <p>You Will be redirected to Login Page</p>
                     </div>
                 </div>
                 <div class="modal-footer">
                     <button type="button" class="button1" data-bs-dismiss="modal">No</button>
                     <a href="{{ route('signout') }}"><button type="submit" class=" m-0 button2">Yes</button></a>
                 </div>
             </div>
         </div>
     </div>
     <!--------logout- end---->
	




<button id="Modal_Popup_Button" data-bs-toggle="modal" class="d-none" ></button>