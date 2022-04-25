<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use \Znck\Eloquent\Traits\BelongsToThrough;

class Product extends Model
{
    use HasFactory;
    //use \Znck\Eloquent\Traits\BelongsToThrough;

    protected $fillable = [
        'category_id','shop_id', 'name', 'description', 'price', 'brand',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image()
    {
        return $this->hasMany(Productimage::class);
    }
    public function options()
    {
        return $this->hasMany(Option::class);
    }
    public function pvariant()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /*public function shop()
    {
        return $this->belongsToThrough(Shop::class, Category::class);
    }*/
}
