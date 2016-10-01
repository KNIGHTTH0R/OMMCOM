<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateUserTypeFormRequest extends Request
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
            'name' => 'required|alpha|max:250|unique:user_types,name,'.$this->get('id'),
        ];
    }
}
