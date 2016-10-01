<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateCitizenFormRequest extends Request
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
            'name'                  => 'required|max:250',
            'slug'                  => 'required',
            'email'                 => 'email',
            'description'           => 'required|max:250',
            'file_path'             => 'mimes:jpeg,jpg,png,gif,flv,mp4,3gp,ts,mov,avi,wmv',
            'captcha'               => 'required|captcha'
        ];
    }
}
