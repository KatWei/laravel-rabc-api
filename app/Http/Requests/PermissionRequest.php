<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
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
                    'name' => ['required', 'unique:permissions,name'],
                    'slug' => 'required'
                ];
                break;
            case 'PATCH':
                $permission = $this->route('permission');
                return [
                    'name' => ['required', 'unique:permissions,name,'. $permission->id],
                    'slug' => 'required'
                ];
                break;
        }
    }

    public function messages()
    {
        return [
            'name.required' => '名称不能为空',
            'name.unique'   => '名称已存在',
            'slug.required' => '标识不能为空'
        ];
    }
}
