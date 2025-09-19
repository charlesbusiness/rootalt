<?php

namespace Modules\ProductManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ProductManager\Database\Factories\PlanProductAccessLevelFactory;

class PlanProductAccessLevel extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    // protected static function newFactory(): PlanProductAccessLevelFactory
    // {
    //     // return PlanProductAccessLevelFactory::new();
    // }
}
