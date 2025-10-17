<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|regex:/^[\pL\s\-]+$/u|max:255',
            'mobile' => 'required|numeric|digits:10',
            'image' => 'image'
        ];
    }

    public function messages(){
        return [
            'name.required' => "Name is required",
            'name.regex' => "Valid Name is required",
            'name.max' => "Valid Name is required",
            'mobile.required' => 'Mobile is required',
            'mobile.numeric' => 'Valid Mobile is required',
            'mobile.digits' => 'Valid Mobile is required',
            'image.image' => 'Valid Image is required'
        ];
    }
}
