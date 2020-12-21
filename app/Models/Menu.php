<?php

namespace App\Models;

use App\Helpers\ModelTree;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    use ModelTree {
        ModelTree::boot as treeBoot;
    }

    protected $fillable = ['parent_id', 'order', 'name', 'icon', 'path', 'permission'];

    /**
     * Detach models from the relationship.
     *
     * @return void
     */
    protected static function boot()
    {
        static::treeBoot();

//        static::deleting(function ($model) {
//            $model->roles()->detach();
//        });
    }

}
