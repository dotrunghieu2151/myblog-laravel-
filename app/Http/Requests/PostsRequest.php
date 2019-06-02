<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostsRequest extends FormRequest
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
            "title" => 'required',
            "body" => 'required',
            "cover_image" =>'image|nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
            "gender" => 'required|regex:/[0-2]/'
        ];
    }
    public function messages()
    {
        return [
          "title.required" => "Please enter the post's title",
          "body.required" => "The post needs a body",
          "gender.required" => "Please specify your gender"
        ];
    }
}
