<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\CmsPage;
use App\Models\Contact;
use App\Models\BusinessContact;
use App\Models\Mails;
use Validator;

class CmsController extends Controller
{
    public function staticpages(){
        $currentPath= Route::getFacadeRoot()->current()->uri();
        $availableRoutes = CmsPage::Where('status',1)->get()->pluck('url')->toArray();
        if(in_array($currentPath,$availableRoutes)){
            $details = CmsPage::where('url',$currentPath)->first();
            $title = $details->meta_title;
            $meta_title = $details->meta_title;
            $meta_keyword = $details->meta_keywords;
            $meta_description = $details->meta_description;
            return view('front.pages.cms_pages.cms_page')->with(compact('title','meta_title','meta_keyword','meta_description','details'));
        }else{
            abort(404);
        }
    }

    public function aboutus(){
        $title ="About Us";
        $meta_title = "About On-Vers - India's Premier Fashion & Activewear Brand";
        $meta_keyword = "onvers, fashion brand, gym wear, sportswear";
        $meta_description = "Discover On-Vers, a leading Indian brand offering an exclusive collection of fashion-forward gym wear, running apparel and sportswear.";
        return view('front.pages.about_us')->with(compact('title','meta_title','meta_keyword','meta_description'));
    }

    public function contactus(){
        $title ="Contact Us";
        $meta_title = "Contact - On-Vers, Best Clothing Brand";
        $meta_keyword = "onvers, sports clothing brand";
        $meta_description = "On-Vers is one of the leading gym wear & sports clothing brand in India. If you have any query related to our products then you can contact us at anytime.";
        return view('front.pages.contact_us')->with(compact('title','meta_title','meta_keyword','meta_description'));
    }
    public function businessenquiry(){
        $title ="Business ENquiry";
        $meta_title = "Business Enquiries - Bulk & Wholesale Clothing, Become a Dealer - On-Vers";
        $meta_keyword = "business enquiry, bulk clothing, wholesale clothing, distributors, manufacturers, on-vers partnership";
        $meta_description = "Retailers, wholesalers, distributors, and manufacturers - Partner with On-Vers for bulk clothing orders. Submit your business enquiry here.";
        return view('front.pages.business-enquiry')->with(compact('title','meta_title','meta_keyword','meta_description'));
    }

    public function saveContact(Request $request) {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255',
                'subject' => 'required',
				'mobile' => 'required|numeric|digits:10',
                'message' => 'required'
            ], [
                'email.email' => 'This email is not a valid email address'
            ]);

             if ($validator->passes()) {
                 // Save Contact
				$contact = new Contact;
				$contact->name = $request->name;
				$contact->email = $request->email;
				$contact->mobile = $request->mobile;
				$contact->subject = $request->subject;
				$contact->message = $request->message;
				$contact->save();

				if (env('MAIL_MODE') == "live") {
					Mails::contact_mail($contact->id);
				}

				return response()->json([ 'status' => true, 'message' => array('Your details has been submitted successfully. We will get back to you soon.') ]);
				
            }else{
				return response()->json(['status' => false,'type' => 'validation','errors' => $validator->messages() ]); 
           
             }  
               
                
           
        
        }
    }

    public function businessSaveContactOld(Request $request){
        if($request->ajax()){
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'mobile' => 'required|numeric|digits:10',
                'email' => 'required|string|email|max:255',
                'message' => 'bail|required',
                'estimated_order_quantity' => 'nullable|numeric',
                'business_type' => 'required|string|in:Retailer,Wholesaler,Distributor,Manufacturer,Other'
            ], [
                'email.regex' => 'This email is not a valid email address'
            ]);

            if($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'errors' => $validator->errors()
                ]);
            }

            $data = $request->all();
            $contact = new BusinessContact;
            $contact->name = $data['name']; 
            $contact->company_name = $data['company_name']; 
            $contact->city = $data['city']; 
            $contact->state = $data['state']; 
            $contact->email = $data['email']; 
            $contact->mobile = $data['mobile']; 
            $contact->message = $data['message']; 
            $contact->estimated_order_quantity = $data['estimated_order_quantity'] ?? null;
            $contact->business_type = $data['business_type']; 
            $contact->save();

            if(env('MAIL_MODE') == "live"){
                $emails = array('customercare@onvers.com');
                $bcc = ['sumit@royalways.com','kapilseth@versatilegroup.in'];
                $messageData = [
                    'data' => $contact
                ];
                Mail::send('emails.business-contact-email', $messageData, function($message) use ($emails,$bcc){
                    $message->to($emails)->bcc($bcc)->subject('Business Contact Us Information Received');
                });
            }

            return response()->json(['status' => true, 'message' => 'Form submitted successfully!']);
        }
    }

    public function businessSaveContact(Request $request) {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
                'mobile' => 'required|numeric|digits:10',
                'email' => 'required|string|email|max:255',
                'message' => 'bail|required',
                'estimated_order_quantity' => 'nullable|numeric',
                'business_type' => 'required|string|in:Retailer,Wholesaler,Distributor,Manufacturer,Other',
                'hear_about' => 'nullable|string|in:Google,Social Media,Referral,Other'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false, 
                    'errors' => $validator->errors()
                ]);
            }

            // Save the data
            $contact = new BusinessContact;
            $contact->name = $request->name;
            $contact->company_name = $request->company_name;
            $contact->city = $request->city;
            $contact->state = $request->state;
            $contact->email = $request->email;
            $contact->mobile = $request->mobile;
            $contact->message = $request->message;
            $contact->estimated_order_quantity = $request->estimated_order_quantity ?? null;
            $contact->business_type = $request->business_type;
            $contact->hear_about = $request->hear_about;
            $contact->save();

            if (env('MAIL_MODE') == "live") {
                $emails = ['customercare@onvers.com'];
                $bcc = ['sumit@royalways.com','kapilseth@versatilegroup.in','jaspreet@rtpltech.com'];
                $messageData = ['data' => $contact];
                Mail::send('emails.business-contact-email', $messageData, function($message) use ($emails,$bcc) {
                    $message->to($emails)->bcc($bcc)->subject('Business Contact Us Information Received');
                });
            }

            return response()->json(['status' => true, 'message' => 'Form submitted successfully!']);
        }
    }

}
