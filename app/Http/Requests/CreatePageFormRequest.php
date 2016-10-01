<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreatePageFormRequest extends Request
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
            'name'          => 'required|max:250|unique:pages,name,'.$this->get('id'),
            'slug'          => 'unique:pages,name,'.$this->get('id'),
            'menu_id'       => 'required',
            'meta_desc'     => 'required|unique:pages,meta_desc,'.$this->get('id'),
            'meta_key'      => 'required|unique:pages,meta_key,'.$this->get('id')
            
        ];
    }
}
