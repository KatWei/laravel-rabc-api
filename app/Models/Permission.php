<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission as ParentPermission;

class Permission extends ParentPermission
{
    protected $appends = ['method_color'];

    public function getMethodColorAttribute()
    {
        $value = $this->attributes['http_method'];
        switch ($value){
            case "GET":
                $color = "green";
                break;
            case "POST":
                $color = "orange";
                break;
            case "PUT":
            case "PATCH":
            case "OPTIONS":
                $color = "gray";
                break;
            case "DELETE":
                $color = "red";
                break;
            default:
                $color = "blue";
                break;
        }
        return $color;
    }

    public function shouldPassThrough(Request $request): bool
    {
        if (empty($this->http_method) && empty($this->http_path)) {
            return true;
        }

        $method = $this->http_method;

        $matches = array_map(function ($path) use ($method) {
            $path = 'api'.$path;

            if (Str::contains($path, ':')) {
                list($method, $path) = explode(':', $path);
                $method = explode(',', $method);
            }

            return compact('method', 'path');
        }, explode("\n", $this->http_path));
        foreach ($matches as $match) {
            if ($this->matchRequest($match, $request)) {
                return true;
            }
        }

        return false;
    }

    protected function matchRequest(array $match, Request $request): bool
    {
        if ($match['path'] == '/') {
            $path = '/';
        } else {
            $path = trim($match['path'], '/');
        }
        if (!$request->is($path)) {
            return false;
        }
        $method = collect($match['method'])->filter()->map(function ($method) {
            return strtoupper($method);
        });

        return $method->isEmpty() || $method->contains($request->method());
    }
}
