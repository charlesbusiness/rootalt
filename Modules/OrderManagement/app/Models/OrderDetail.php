<?php

namespace Modules\OrderManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\ProductManager\Models\Product;

// use Modules\OrderManagement\Database\Factories\OrderDetailsFactory;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_manager_id',
        'product_id',
        'qty',
        'cost_per_item',
        'line_total'
    ];

    public function order()
    {
        return $this->belongsTo(OrderManager::class, 'order_manager_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
