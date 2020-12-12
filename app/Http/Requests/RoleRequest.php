<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
                    'name' => 'required|unique:roles'
                ];
            case 'PATCH':
                $role = $this->route('role');
                return [
                    'name' => 'required|unique:roles,id,'.$role->id
                ];
        }
    }

    public function messages()
    {
        return [
            'name.required' => '角色名不能为空',
            'name.unique' => "角色名已存在"
        ];
    }
}
