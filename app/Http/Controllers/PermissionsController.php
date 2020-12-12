<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionsController extends ApiController
{
    public function index(Permission $permission)
    {
        $permissions = $permission
            ->latest()
            ->paginate(request()->get('pageSize', 20));
        return PermissionResource::collection($permissions);
    }

    public function store(PermissionRequest $request, Permission $permission)
    {
        $permission->fill($request->all());
        $permission->http_method = !empty($permission->http_method) ? implode(",", $permission->http_method) : null;
        $permission->save();
        return $this->message("添加成功");
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->fill($request->all());
        $permission->http_method = !empty($permission->http_method) ? implode(",", $permission->http_method) : null;
        $permission->save();
        return $this->message("修改成功");
    }

    public function destroy($id)
    {
        if($id == "ids_destroy") {
            Permission::destroy(request('ids'));
        } else {
            Permission::destroy($id);
        }

        return $this->message("删除成功！");
    }


    public function getRoleByPermissions($roleId = null)
    {
        if($roleId) {
            $permissions = Permission::with('roles')->where('roles.id', $roleId)->get();
        } else {
            $permissions = Permission::all();
        }
        return $this->success(PermissionResource::collection($permissions));
    }
}
