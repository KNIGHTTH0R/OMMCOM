<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class RegisterRequest extends Request
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
            'contact_no'            => 'required|numeric',
            'user_type_id'          => 'required',
            'email'                 => 'required|email|unique:users,email,'.$this->get('id'),
            'password'              => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'profile_image'         => 'mimes:jpeg,jpg,png,gif',
            //'dob'           => 'required',
            //'age'           => 'required|numeric',            
        ];
    }
}
