<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Resources\RoleResource;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesController extends ApiController
{
    /**
     * @OA\Get(path="/api/roles",description="角色列表",tags={"角色"},security={{"bearer_token": {}}},summary="角色列表",
     *    @OA\Parameter(name="name",in="query", description="角色", example="超级管理员",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="角色列表")
     * )
     * @param Role $role
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Role $role)
    {
        $roles = $role->with("permissions")->when($name = request('name'), function($q) use ($name){
            $q->where('name', 'like', "$name%");
        })
        ->latest()
        ->paginate(request()->get('pageSize', 20));
        return RoleResource::collection($roles);
    }

    /**
     * @OA\Post(path="/api/roles",description="添加角色",summary="添加角色",security={ {"bearer": {} }},tags={"角色"},
     *     @OA\Parameter(name="name",in="query", description="角色名",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="permission_ids",in="query", description="权限ids",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="添加成功"),
     *     @OA\Response(response=422,description="错误的凭证响应")
     * )
     * @param RoleRequest $request
     * @param Role $role
     * @param Permission $permission
     * @return mixed
     */
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

    /**
     * @OA\Patch(path="/api/roles/{role}",description="修改角色",summary="修改角色",security={ {"bearer": {} }},tags={"角色"},
     *     @OA\Parameter(name="id",in="query", description="角色id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="name",in="query", description="角色名称",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="permission_ids",in="query", description="权限ids",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="修改成功"),
     * )
     * @param RoleRequest $request
     * @param Role $role
     * @param Permission $permission
     * @return mixed
     */
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
     * @OA\Delete(path="/api/roles/ids_destroy",description="删除角色",summary="删除角色",security={ {"bearer": {} }},tags={"角色"},
     *     @OA\Parameter(name="ids",in="query", description="角色id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Response(response=200,description="删除成功"),
     * )
     * @param $id
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
