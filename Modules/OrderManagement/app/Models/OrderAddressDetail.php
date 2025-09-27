<?php

namespace Modules\OrderManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\OrderManagement\Database\Factories\OrderAddressDetailFactory;

class OrderAddressDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [];

    // protected static function newFactory(): OrderAddressDetailFactory
    // {
    //     // return OrderAddressDetailFactory::new();
    // }
}
