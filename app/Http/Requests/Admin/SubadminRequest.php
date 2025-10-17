<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Admin;

class SubadminRequest extends FormRequest
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
            'image' => 'image',
            'email' => 'required'
        ];
    }

    public function messages(){
        return [
            'name.required' => 'Name is required',
            'mobile.required' => 'Mobile is required',
            'mobile.numeric' => 'Valid Mobile is required',
            'image.image' => 'Valid Image is required',
            'email.required' => 'Email is required',
            'email.required' => 'Valid Email is required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if($this->input('id')==""){
                $subadminCount = Admin::where('email',$this->input('email'))->count();
                if($subadminCount>0){
                    $validator->errors()->add('email', 'Subadmin already exists!');
                }
            }
        });
    }

    protected function failedValidation(Validator $validator){
        throw new HttpResponseException(
            redirect()->back()
                ->withErrors($validator)
                ->withInput()
        );
    }
}
