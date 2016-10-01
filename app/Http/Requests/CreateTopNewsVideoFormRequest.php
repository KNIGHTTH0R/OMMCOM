<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateTopNewsVideoFormRequest extends Request
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
            'name'          => 'required',
            'video_file'    => 'required|mimes:flv,mp4,3gp,ts,mov,avi,wmv',
        ];
    }
}
