<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model {
    use HasFactory, SoftDeletes;

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getSellPriceAttribute()
    {
        $item = $this->item;
        $itemPrice = $this->store_item_price ?? $item->default_price;
        return $itemPrice + $this->markup_price;
    }

    public function getDisplaySellPriceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->sell_price);
    }

    public function getMarkupPriceAttribute()
    {
        $item = $this->item;
        $itemPrice = $this->store_item_price ?? $item->default_price;
        $itemMarkup = $this->store_item_markup ?? $item->default_markup;
        return $itemPrice * $itemMarkup;
    }

    public function getDisplayMarkupPriceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->markup_price);
    }

    public function getPotentialProfitAttribute()
    {
        return $this->item_quantity * $this->markup_price;
    }

    public function getDisplayPotentialProfitAttribute()
    {
        return CurrencyHelper::formatCurrency($this->potential_profit);
    }

}
