<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;

class MenusController extends ApiController
{
    /**
     * @OA\Get(path="/api/menus",description="菜单列表",tags={"菜单"},security={{"bearer_token": {}}},summary="菜单列表",
     *     @OA\Response(response=200,description="角色列表")
     * )
     * @param Menu $menu
     * @return mixed
     */
    public function index(Menu $menu)
    {
        $menus = Menu::paginate(request()->get('pageSize', 20));
        return $this->success(MenuResource::collection($menus));
    }

    public function getAllMenu()
    {
        $menu = new Menu();
        $menus = $menu->selectOptions();
        return $this->success($menus);
    }

    /**
     * @OA\Post(path="/api/menus",description="添加菜单",summary="添加菜单",security={ {"bearer": {} }},tags={"菜单"},
     *     @OA\Parameter(name="name",in="query", description="菜单名",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="parent_id",in="query", description="上级菜单id",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="添加成功"),
     *     @OA\Response(response=422,description="错误的凭证响应")
     * )
     * @param MenuRequest $request
     * @param Menu $menu
     * @return mixed
     */
    public function store(MenuRequest $request, Menu $menu)
    {
        $menu->fill($request->all());
        $menu->save();

        return $this->success('添加成功');
    }

    /**
     * @OA\Post(path="/api/menus/{menu}",description="添加菜单",summary="添加菜单",security={ {"bearer": {} }},tags={"菜单"},
     *     @OA\Parameter(name="id",in="query", description="菜单id",required=true, example="1",@OA\Schema(type="integer")),
     *     @OA\Parameter(name="name",in="query", description="菜单名",required=true,@OA\Schema(type="string")),
     *     @OA\Parameter(name="parent_id",in="query", description="上级菜单id",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="添加成功"),
     *     @OA\Response(response=422,description="错误的凭证响应")
     * )
     * @param MenuRequest $request
     * @param Menu $menu
     * @return mixed
     */
    public function update(MenuRequest $request, Menu $menu)
    {
        $menu->update($request->all());
        return $this->success('修改成功');
    }
}
