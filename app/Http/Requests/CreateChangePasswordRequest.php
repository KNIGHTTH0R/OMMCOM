<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateChangePasswordRequest extends Request
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
            'old_password'   => 'required', 
            'new_password'   => 'required|min:8|regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/|different:old_password',
            'conf_password'  => 'required|same:new_password|min:8',
        ];
    }
}
