<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateNewsFormRequest extends Request
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
            'slug'                  => 'required|unique:news,slug,'.$this->get('id'),
            'cat_id'                => 'required',
            //'short_description'     => 'required',
            //'long_description'      => 'required',
            'featured_image'        => 'mimes:jpeg,jpg,png,gif',
            'file_path'             => 'mimes:jpeg,jpg,png,gif,flv,mp4,3gp,ts,mov,avi,wmv',
            'attachment_file'       => 'mimes:doc,docx,pdf',
        ];
    }
}
