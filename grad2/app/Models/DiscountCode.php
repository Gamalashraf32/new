<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id', 'code', 'type','value','minimum_requirements_value','starts_at','ends_at'
    ];
    protected $dates = ['ends_at','starts_at'];
    public function user(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
