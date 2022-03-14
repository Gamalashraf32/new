<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'name', 'description'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
