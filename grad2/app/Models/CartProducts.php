<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProducts extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id', 'cart_id', 'product_id', 'variant_id1', 'variant_id2', 'product_name',
        'quantity', 'price'
    ];
}
