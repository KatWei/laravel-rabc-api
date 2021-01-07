<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionsController extends ApiController
{
    /**
     * @OA\Get(path="/api/permissions",description="权限列表",tags={"权限"},security={{"bearer_token": {}}},summary="权限列表",
     *     @OA\Response(response=200,description="权限列表")
     * )
     * @param Permission $permission
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Permission $permission)
    {
        $permissions = $permission
            ->latest()
            ->paginate(request()->get('pageSize', 20));
        return PermissionResource::collection($permissions);
    }


    /**
     * @OA\Post(path="/api/permissions",description="添加权限",summary="添加权限",security={ {"bearer": {} }},tags={"权限"},
     *     @OA\Parameter(name="name",in="query", description="权限名称",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="slug",in="query", description="权限别称",required=true,@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="添加成功"),
     *     @OA\Response(response=422,description="错误的凭证响应")
     * )
     * @param PermissionRequest $request
     * @param Permission $permission
     * @return mixed
     */
    public function store(PermissionRequest $request, Permission $permission)
    {
        $permission->fill($request->all());
        $permission->http_method = !empty($permission->http_method) ? implode(",", $permission->http_method) : null;
        $permission->save();
        return $this->message("添加成功");
    }

    /**
     * @OA\Patch(path="/api/permissions/{permission}",description="修改权限",summary="修改权限",security={ {"bearer": {} }},tags={"权限"},
     *     @OA\Parameter(name="id",in="query", description="权限id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="name",in="query", description="权限名称",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="slug",in="query", description="权限别称",required=true,@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="修改成功"),
     * )
     * @param PermissionRequest $request
     * @param Permission $permission
     * @return mixed
     */
    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission->fill($request->all());
        $permission->http_method = !empty($permission->http_method) ? implode(",", $permission->http_method) : null;
        $permission->save();
        return $this->message("修改成功");
    }

    /**
     * @OA\Delete(path="/api/permissions/ids_destroy",description="删除权限",summary="删除权限",security={ {"bearer": {} }},tags={"权限"},
     *     @OA\Parameter(name="ids",in="query", description="权限id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Response(response=200,description="删除成功"),
     * )
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        if($id == "ids_destroy") {
            Permission::destroy(request('ids'));
        } else {
            Permission::destroy($id);
        }

        return $this->message("删除成功！");
    }


    /**
     * @OA\Get(path="/api/permissions/role_by_permissions/{roleId?}",description="根据角色获取拥有权限",summary="根据角色获取拥有权限",security={ {"bearer": {} }},tags={"权限"},
     *     @OA\Parameter(name="role_id",in="query", description="角色id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Response(response=200,description="权限列表")
     * )
     * @param null $roleId
     * @return mixed
     */
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
