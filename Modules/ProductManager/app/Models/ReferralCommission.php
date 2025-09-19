<?php

namespace Modules\ProductManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ProductManager\Database\Factories\ReferralCommissionFactory;

class ReferralCommission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    // protected static function newFactory(): ReferralCommissionFactory
    // {
    //     // return ReferralCommissionFactory::new();
    // }
}
