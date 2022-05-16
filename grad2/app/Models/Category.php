<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'shop_id', 'name', 'description','extra_shipping'
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function shops()
    {
        return $this->belongsTo(Shop::class);
    }
    public function option()
    {
        return $this->hasManyThrough(Option::class,Product::class);
    }
    public function variant ()
    {
        return $this->hasManyThrough(ProductVariant::class,Product::class);
    }

}
