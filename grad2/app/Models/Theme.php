<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_id',
        'name',
        'font',
        'primary_color',
        'secondary_color',
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
