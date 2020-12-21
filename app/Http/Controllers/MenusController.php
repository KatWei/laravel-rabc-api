<?php

namespace App\Http\Controllers;

use App\Http\Requests\MenuRequest;
use App\Http\Resources\MenuResource;
use App\Models\Menu;

class MenusController extends ApiController
{
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

    public function store(MenuRequest $request, Menu $menu)
    {
        $menu->fill($request->all());
        $menu->save();

        return $this->success('添加成功');
    }

    public function update(MenuRequest $request, Menu $menu)
    {
        $menu->update($request->all());
        return $this->success('修改成功');
    }
}
