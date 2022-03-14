<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    public function shopowners(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ShopOwner::class);
    }
}
