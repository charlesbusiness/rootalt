<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Core\Database\Factories\PaymentOptionFactory;

class PaymentOption extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name','key'];

    protected array $options = [
        [
            'key' => 'mastercard',
            'name' => 'mastercard'
        ],

        [
            'key' => 'mobile_money',
            'name' => 'mobile money'
        ],

        [
            'key' => 'tipme',
            'name' => 'liberia tipme'
        ],
    ];

    public function getPaymentOptions(){
      return $this->options;
    }
}
