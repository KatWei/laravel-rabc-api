<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminUserRequest extends FormRequest
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
                    'name' => 'required|unique:admin_users, name',
                    'username' => 'required|unique:admin_users, username',
                    'password' => 'required|confirmed|min:6'
                ];
            case 'PATCH':
                $user = $this->route('admin_user');
                return [
                    'name' => 'required|unique:admin_users,name,'.$user->id,
                    'username' => 'required|unique:admin_users,username,'.$user->id,
                ];
        }
    }

    public function messages()
    {
        return [
            'name.required' => '用户名必须填写',
            'name.unique' => '用户名已存在',
            'username.unique' => "昵称已存在",
            'password.min' => '密码至少为 6 位',
            'password.confirmed' => "密码不一致"
        ];
    }
}
