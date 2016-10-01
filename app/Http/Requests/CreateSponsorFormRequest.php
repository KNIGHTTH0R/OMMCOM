<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateSponsorFormRequest extends Request
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
            'name'              => 'required|alpha|max:250',
            'mail_id'           => 'required|email|max:250',
            'contact_number'    => 'required|numeric|min:10',
            'city'              => 'required|max:250',
            'zip'               => 'required|numeric',
            'city'              => 'required|max:250',
        ];
    }
}
