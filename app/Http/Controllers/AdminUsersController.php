<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminUserRequest;
use App\Http\Resources\AdminUserResource;
use App\Models\AdminUser;
use App\Models\Menu;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class AdminUsersController extends ApiController
{
    /**
     * @OA\Get(path="/api/admin_users",description="用户列表",tags={"用户"},security={{"bearer_token": {}}},summary="用户列表",
     *     @OA\Parameter(name="name",in="query", description="用户名", example="admin",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="用户列表")
     * )
     * @param AdminUser $adminUser
     * @return mixed
     */
    public function index(AdminUser $adminUser)
    {
        $users = $adminUser->when($name = request('name'), function($q) use ($name){
            $q->where('name', 'like', "$name%");
        })
            ->latest()
            ->paginate(request()->get('pageSize', 20));

        return $this->success(AdminUserResource::collection($users));
    }

    /**
     * @OA\Get(path=" api/user",description="个人用户信息",tags={"用户"},security={{"bearer_token": {}}},summary="个人用户信息",
     *     @OA\Response(response=200,description="用户列表")
     * )
     * @return mixed
     */
    public function show()
    {
        $user = auth('admin-api')->user();
        $menu = new Menu();
        $menuTree = $menu->toTree();
        if(!isset($menuTree))
            $user['menus'] = $this->filterMenu($menuTree, $user);
        return $this->success($user);
    }

    protected function filterMenu($menus, $user)
    {
        foreach($menus as $menu) {
            if($user->can($menu['permission'])) {
                if(!isset($menu['children'])) {
                    $filterMenus[] = $menu;
                }else {
                    $menu['children'] = $this->filterMenu($menu['children'], $user);
                    $filterMenus[] = $menu;
                }
            }
        }
        return $filterMenus;
    }


    /**
     * @OA\Post(path="/api/admin_users",description="添加用户",summary="添加用户",security={ {"bearer": {} }},tags={"用户"},
     *     @OA\Parameter(name="name",in="query", description="用户名称",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="username",in="query", description="用户昵称",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="password",in="query", description="密码",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="role_ids",in="query", description="角色ids",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="添加成功"),
     *     @OA\Response(response=422,description="错误的凭证响应")
     * )
     */
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

    /**
     * @OA\Patch(path="/api/admin_users/{admin_user}",description="修改用户",summary="修改用户",security={ {"bearer": {} }},tags={"用户"},
     *     @OA\Parameter(name="id",in="query", description="用户id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="name",in="query", description="用户名称",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="username",in="query", description="用户昵称",required=true,@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="修改成功"),
     * )
     * @param AdminUserRequest $request
     * @param AdminUser $adminUser
     * @param Role $role
     * @return mixed
     */
    public function update(AdminUserRequest $request, AdminUser $adminUser, Role $role)
    {
        $adminUser->update($request->all());
        $role_ids = $request->role_ids;
        //角色
        if(!empty($role_ids)) {
            $roles = $role->whereIn('id', $request->role_ids)->get();
            $adminUser->syncRoles($roles);
        }
        return $this->success("修改成功");
    }

    /**
     * @OA\Delete(path="/api/admin_users/ids_destroy",description="删除用户",summary="删除用户",security={ {"bearer": {} }},tags={"用户"},
     *     @OA\Parameter(name="ids",in="query", description="用户id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Response(response=200,description="删除成功"),
     * )
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        if($id == "ids_destroy") {
            collect(request('ids'))->map(function($id){
                $user = AdminUser::findOrFail($id);
                $user->roles()->detach();
                $user->delete();
            });
        } else {
            $user = AdminUser::findOrFail($id);
            $user->roles()->detach();
            $user->delete();
        }


        return $this->message("删除成功");
    }
}
