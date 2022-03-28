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

    public function getTotalPotentialProfitAttribute()
    {
        return $this->inventories->pluck('potential_profit')->sum();
    }

    public function getDisplayTotalPotentialProfitAttribute()
    {
        return CurrencyHelper::formatCurrency($this->total_potential_profit);
    }

    public function getTotalInventoryValueAttribute()
    {
        return $this->inventories->pluck('inventory_value')->sum();
    }

    public function getDisplayTotalInventoryValueAttribute()
    {
        return CurrencyHelper::formatCurrency($this->total_inventory_value);
    }

    public function getFullJsonResponseArray()
    {
        return $this->only([
            'name',
            'ean13_bar_code',
            'default_price',
            'default_markup',
            'default_markup_price',
            'display_default_markup_price',
            'default_sell_price',
            'display_default_sell_price',
            'total_potential_profit',
            'display_total_potential_profit',
            'total_inventory_value',
            'display_total_inventory_value',
        ]);
    }

    public function getSimplifiedJsonResponseArray()
    {
        return $this->only([
            'name',
            'ean13_bar_code',
        ]);
    }

}
