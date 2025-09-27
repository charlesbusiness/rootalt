<?php

namespace Modules\ProductManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\ProductManager\Database\Factories\ProductMovementFactory;

class ProductMovement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    public function makeMovement($product, $transactionType, $qty, $reason)
    {
        return  self::create([
            'product_id' => $product->id,
            'transaction_type' => $transactionType,
            'qty' => $qty,
            'reason' => $reason,
            'performed_by' => auth('sactum')->id,
        ]);
    }
}
