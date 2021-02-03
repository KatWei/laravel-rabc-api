<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Permission
{
    protected $middlewarePrefix = 'admin.permission';
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param array $args
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$args)
    {
        $user = auth('admin-api')->user();
        if(!$user || !empty($args) || $this->shouldPassThrough($request)) {
            return $next($request);
        }
//
//        if ($this->checkRoutePermission($request)) {
//            return $next($request);
//        }
        if (!$user->getAllPermissions()->first(function ($permission) use ($request) {
            return $permission->shouldPassThrough($request);
        })) {
            throw new AuthorizationException();
        }
        return $next($request);
    }

    public function checkRoutePermission(Request $request)
    {
        if (!$middleware = collect($request->route()->middleware())->first(function ($middleware) {
            return Str::startsWith($middleware, $this->middlewarePrefix);
        })) {
            return false;
        }

        $args = explode(',', str_replace($this->middlewarePrefix, '', $middleware));
        $method = array_shift($args);
        if (!method_exists(Checker::class, $method)) {
            throw new \InvalidArgumentException("Invalid permission method [$method].");
        }

        call_user_func_array([Checker::class, $method], [$args]);

        return true;
    }

    protected function shouldPassThrough($request)
    {
        // 下面这些路由不验证权限
        $excepts = [
            'auth/login',
            'auth/logout',
        ];
        return collect($excepts)
            ->map(function($path, $key) {
                return env('APP_URL').'/api/'.$path;
            })
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }
                return $request->is($except);
            });
    }
}
