<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateConferenceFormRequest extends Request
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
            'started_at'            => 'required',
            'start_time'            => 'required',
            'end_time'              => 'required',
            'short_desc'            => 'required',
            'featured_image'        => 'mimes:jpeg,jpg,bmp,png,gif',
            'conference_banner'     => 'mimes:jpeg,jpg,bmp,png,gif',
            'slug'                  => 'required|unique:conferences,slug,'.$this->get('id'),
        ];
    }
}
