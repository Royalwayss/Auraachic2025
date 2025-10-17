<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Category;

class CategoryRequest extends FormRequest
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
            'category_name' => 'required',
            'url' => 'required|regex:/^[\pL\s\-]+$/u'
        ];
    }

    public function messages(){
        return [
            'category_name.required' => 'Category Name is required',
            'url.required' => 'Category URL is required'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            //echo $this->input('id'); die;
            if($this->input('id')!=""){
                $categoryCount = Category::where(function ($query) {
                    $query->where('url', $this->input('url'))
                          ->where('id', '!=', $this->input('id'));
                })->count();
            }else{
                $categoryCount = Category::where(function ($query) {
                    $query->where('url', $this->input('url'));
                })->count();
            }
            if($categoryCount>0){
                $validator->errors()->add('url', 'Category already exists!');
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
