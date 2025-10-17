<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class WebsiteSettingsRequest extends FormRequest
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
            'invoice_email' => 'required|email|max:255',
            'invoice_mobile' => 'bail|required',
            'invoice_address' => 'bail|required',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            
        });
    }

}
