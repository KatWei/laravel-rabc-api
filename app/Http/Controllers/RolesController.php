<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends ApiController
{
    public function index(Role $role)
    {
        $roles = $role->with("permissions")->when($name = request('name'), function($q) use ($name){
            $q->where('name', 'like', "$name%");
        })
        ->latest()
        ->paginate(request()->get('pageSize', 20));
        return RoleResource::collection($roles);
    }

    public function store(RoleRequest $request, Role $role, Permission $permission)
    {
        $role->create([
            'name' => $request->name,
        ]);
        $permissions = $permission->whereIn('id', $request->get('permission_ids'))->get();
        $role = $role->where(['name' => $request->get('name')])->first();
        $role->syncPermissions($permissions);
        return $this->success(RoleResource::make($role));
    }

    public function update(RoleRequest $request, Role $role, Permission $permission)
    {
        $role->update([
            'name' => $request->name
        ]);
        $permissions = $permission->whereIn('id', $request->permission_ids)->get();
        $role->syncPermissions($permissions);
        return $this->message("修改成功！");
    }

    /**
     * 删除
     * @param $id
     *
     * @return mixed
     */
    public function destroy($id)
    {
        if($id == "ids_destroy") {
            collect(request('ids'))->map(function($id){
                $role = Role::findOrFail($id);
                $role->permissions()->detach();
                $role->delete();
            });
        } else {
            $role = Role::findOrFail($id);
            $role->permissions()->detach();
            $role->delete();
        }

        return $this->message("删除成功！");
    }


    /**
     * 获取所有角色
     * @return mixed
     */
    public function getAllRole()
    {
        $roles = Role::all();
        return $this->success(RoleResource::collection($roles));
    }

}
