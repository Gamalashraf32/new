<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Shop extends Model
{
    protected $fillable = [
        'shop_owner_id'
    ];
    use HasFactory;

    public function shopowner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ShopOwner::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(theme::class);
    }

    public function discountcode(): HasMany
    {
        return $this->hasMany(DiscountCode::class);
    }
    public function category()
    {
        return $this->hasMany(Category::class);
    }
}
