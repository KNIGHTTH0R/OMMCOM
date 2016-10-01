<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateSettingsMailFormRequest extends Request
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
            'from_mail_id' => 'required|email|max:250',
            'from_name' => 'required|alpha|max:250',
            'password' => 'required|min:8',
            'smtp_port' => 'required|numeric|min:2',
        ];
    }
}
