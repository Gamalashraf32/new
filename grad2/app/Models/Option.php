<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;
    protected $fillable = [
         'name','product_id'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function prvariant()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function cat()
    {
        return $this->belongsToThrough(Category::class,Product::class);
    }

    use HasFactory;
}
