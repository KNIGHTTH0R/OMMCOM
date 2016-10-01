<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateAdvertisementFormRequest extends Request
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
            'name'                  => 'required|max:250|unique:advertisements,name,'.$this->get('id'),
            'sponsor_id'            => 'required|max:250',
            'start_date'            => 'required',
            'end_date'              => 'required',
            'advertisement_type_id' => 'required',
            'file_path'             => 'required|mimes:jpeg,png,gif,bmp,mpga,mp3,ogg,mp4,wav,mov,audio/mpeg'
        ];
    }
}
