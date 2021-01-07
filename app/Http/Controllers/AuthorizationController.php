<?php

namespace App\Http\Controllers;


use App\Models\AdminUser;
use Illuminate\Http\Request;
use Validator;

class AuthorizationController extends ApiController
{
    /**
     * @OA\Post(path="/api/auth/login",description="登录",summary="登录",tags={"登录"},
     *     @OA\Parameter(name="name",in="query", description="用户名",required=true, example="admin",@OA\Schema(type="string")),
     *     @OA\Parameter(name="password",in="query", description="密码",required=true, example="123456",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="token 凭证")
     * )
     * @param Request $request
     * @return mixed
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'password' => 'required|string|min:6',
        ]);
        if($validator->fails()){
            return $this->failed($validator->errors()->first(), 422);
        }
        if(! $token = auth('admin-api')->attempt($validator->validated())){
            return $this->failed('Unauthorized', 401);
        }
        return $this->createNewToken($token);
    }

    /**
     * @OA\Post(path="/api/auth/register",description="注册",summary="注册",tags={"登录"},
     *     @OA\Parameter(name="name",in="query", description="用户名",required=true, example="admin",@OA\Schema(type="string")),
     *     @OA\Parameter(name="username",in="query", description="昵称",required=true, example="超级管理员",@OA\Schema(type="string")),
     *     @OA\Parameter(name="password",in="query", description="密码",required=true, example="123456",@OA\Schema(type="string")),
     *     @OA\Response(response=200,description="用户信息")
     * )
     * @param Request $request
     * @return mixed
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'username'=> 'required',
            'password' => 'required|string|confirmed|min:6'
        ]);
        if($validator->fails()){
            return $this->failed($validator->errors()->first(), 400);
        }

        $user = AdminUser::create($validator->validated());
        return $this->success([
            'user' =>$user
        ]);
    }

    /**
     * @OA\Delete(path="/api/auth/logout",description="退出",security={{"bearer_token": {}}},summary="退出",tags={"登录"},
     *     @OA\Response(response=200,description="退出成功")
     * )
     * @param Request $request
     * @return mixed
     */
    public function logout()
    {
        auth('admin-api')->logout();
        return $this->success("退出成功");
    }

    /**
     * @OA\Post(path="/api/auth/refresh",description="刷新Toekn",security={{"bearer_token": {}}},summary="刷新Toekn",tags={"登录"},
     *     @OA\Response(response=200,description="token 信息")
     * )
     * @return mixed
     */
    public function refresh()
    {
        return $this->createNewToken(auth('admin-api')->refresh());
    }

    /**
     * get the token array structure
     * @param $token
     * @return mixed
     */
    public function createNewToken($token)
    {
        return $this->success([
            'access_token' =>$token,
            'token_type' => 'Bearer',
            'expires_in' => auth('admin-api')->factory()->getTTL() * 60,
            'user' => auth('admin-api')->user()
        ]);
    }
}
