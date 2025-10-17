<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class UpdateWidgetRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'heading' => 'bail|required',
            'type' => 'bail|required',
            'status' => 'bail|required',
            'tab_title' => 'required_if:type,TAB_WISE_PRODUCTS|array',
            'tab_title.*' => 'required_if:type,TAB_WISE_PRODUCTS|string',
            'desktop_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'mobile_image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'video' => 'file|mimes:mp4,mkv,avi|max:8000',
            'products' => 'required_if:type,SINGLE_VIDEO_WITH_MULTIPLE_PRODUCTS,SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS,MULTIPLE_PRODUCTS|array',
            'products.*' => 'integer',
            'categories' => 'required_if:type,PRODUCTS_FROM_CATEGORIES,MULTIPLE_CATEGORIES',
            'categories.*' => 'integer',
            'brands' => 'required_if:type,MULTIPLE_BRANDS|array',
            'brands.*' => 'integer',
            /*'desktop_images' => 'required_if:type,SINGLE_BANNER,SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS|array',
            'desktop_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Each file must be an image
            'mobile_images' => 'required_if:type,SINGLE_BANNER,SINGLE_BANNER_WITH_MULTIPLE_PRODUCTS|array',
            'mobile_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Each file must be an image*/
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            
        });
    }

}
