<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'default_price', 'default_markup',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function getDefaultSellPriceAttribute()
    {
        return $this->default_price + $this->default_markup_price;
    }

    public function getDisplayDefaultSellPriceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->default_sell_price);
    }

    public function getDefaultMarkupPriceAttribute()
    {
        return $this->default_price * $this->default_markup;
    }

    public function getDisplayDefaultMarkupPriceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->default_markup_price);
    }

}
