<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;
    use \Znck\Eloquent\Traits\BelongsToThrough;
    protected $guarded=[];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function options()
    {
        return $this->belongsTo(Option::class);
    }
    public function cate ()
    {
        return $this->belongsToThrough(Category::class,Product::class);
    }
}
