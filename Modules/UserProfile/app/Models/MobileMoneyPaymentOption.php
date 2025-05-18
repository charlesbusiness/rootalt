<?php

namespace Modules\UserProfile\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\UserProfile\Database\Factories\MobileMoneyPaymentOptionFactory;

class MobileMoneyPaymentOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
}
