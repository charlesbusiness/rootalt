<?php

namespace Modules\OrderManagement\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer_id',
        'order_number',
        'year',
        'total_qty',
        'subtotal',
        'tax',
        'discount',
        'shipping_cost',
        'grand_total',
        'status',
        'payment_status',
        'shipping_status'
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function address()
    {
        return $this->hasOne(OrderAddressDetail::class, 'order_manager_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $year = now()->year;

            $latestOrder = self::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = $latestOrder ? intval(substr($latestOrder->order_number, 7)) + 1 : 1;

            $order->order_number = $year . '-' . str_pad($nextNumber, 8, '0', STR_PAD_LEFT);
        });
    }
}
