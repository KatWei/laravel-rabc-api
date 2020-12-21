<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required|unique:menus,name'
                ];
            case 'PATCH':
                $role = $this->route('menu');
                return [
                    'name' => 'required|unique:menus,name,'.$role->id
                ];
        }
    }
}
