<?php

namespace App\Models;

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
}
