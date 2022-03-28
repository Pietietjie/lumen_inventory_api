<?php

namespace App\Models;

use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inventory extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'item_quantity', 'store_item_price', 'store_item_markup',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getPurchasePriceAttribute()
    {
        return $this->store_item_price ?? $this->item->default_price;
    }

    public function getDisplayPurchasePriceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->purchase_price);
    }

    public function getSellPriceAttribute()
    {
        return $this->purchase_price + $this->markup_price;
    }

    public function getDisplaySellPriceAttribute()
    {
        return CurrencyHelper::formatCurrency($this->sell_price);
    }

    public function getMarkupPriceAttribute()
    {
        return $this->purchase_price * ($this->store_item_markup ?? $this->item->default_markup);
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

    public function getFullJsonResponseArray()
    {
        return $this->only([
            'item_quantity',
            'markup_price',
            'display_markup_price',
            'sell_price',
            'display_sell_price',
            'purchase_price',
            'display_purchase_price',
        ]);
    }

}
