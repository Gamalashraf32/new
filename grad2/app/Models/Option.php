<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
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


    use HasFactory;
}
