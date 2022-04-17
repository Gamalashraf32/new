<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id', 'order_id', 'product_id', 'variant_id', 'name',
        'quantity', 'price'
    ];
}