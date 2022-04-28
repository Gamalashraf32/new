<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'government','shop_id','price','duration'
    ];
    use HasFactory;
}
