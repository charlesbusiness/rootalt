<?php

namespace Modules\ProductManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ProductManager\Database\Factories\OrderManagerFactory;

class OrderManager extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OrderManagerFactory
    // {
    //     // return OrderManagerFactory::new();
    // }
}
