<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    /** @use HasFactory<\Database\Factories\SaleFactory> */
    use HasFactory;

    use HasFactory;

    protected $fillable = [
        'total_amount',
        'total_cost',
        'total_profit',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
