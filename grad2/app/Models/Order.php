<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id', 'shop_user_id', 'status', 'note',
        'subtotal_price', 'discounts','shipping_price', 'total_price','extra_shipping','user_balance'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
