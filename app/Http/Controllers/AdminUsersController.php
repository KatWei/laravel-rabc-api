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
    public function index(AdminUser $adminUser)
    {
        $users = $adminUser->with("roles")->when($name = request('name'), function($q) use ($name){
            $q->where('name', 'like', "$name%");
        })
            ->latest()
            ->paginate(request()->get('pageSize', 20));

        return $this->success(AdminUserResource::collection($users));
    }

    public function show()
    {
        $user = auth('admin-api')->user();
        $menu = new Menu();
        $menuTree = $menu->toTree();
        $filterMenu = collect($menuTree)->filter(function($menu, $menu_key) use ($user){
            return $user->can(Arr::get($menu, 'permission'));
        });
        $user['menus'] = $filterMenu->values()->all();
        return $this->success($user);
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
