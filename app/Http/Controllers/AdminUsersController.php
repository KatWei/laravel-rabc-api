<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserRequest;
use App\Models\AdminUser;
use Spatie\Permission\Models\Role;

class AdminUsersController extends ApiController
{
    public function show()
    {
        return $this->success(auth('admin-api')->user());
    }

    public function store(AdminUserRequest $request, AdminUser $adminUser, Role $role)
    {
        $adminUser->fill($request->all());
        $adminUser->save();
        $role_ids = $request->role_ids;
        //角色
        if(!empty($role_ids)) {
            $roles = $role->whereIn('id', $request->role_ids)->get();
            $adminUser->syncRoles($roles);
        }
        // 权限
        return $this->success("添加成功");
    }
}
