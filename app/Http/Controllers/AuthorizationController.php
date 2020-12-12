<?php

namespace App\Http\Controllers;


use App\Models\AdminUser;
use Illuminate\Http\Request;
use Validator;

class AuthorizationController extends ApiController
{
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

    public function logout()
    {
        auth('admin-api')->logout();
        return $this->success("退出成功");
    }

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
