<?php

namespace App\Models;

use App\Events\StoresCreatingEvent;
use App\Helpers\CurrencyHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Store extends Model {
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    public static function boot() {

        parent::boot();
    
        static::creating(function($store) {
            event(new StoresCreatingEvent($store));
        });
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_stores')
            ->withPivot(['user_main_store']);
    }

    public function mainStoreUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_stores')
            ->withPivot(['user_main_store'])
            ->wherePivot('user_main_store', true);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function getThisUserMainStoreAttribute()
    {
        $user = $this->users()->where('users.id', Auth::user()->id)->first();
        return ! empty($user)
            ? $user->pivot->user_main_store
            : 0;
    }

    public function getTotalPotentialProfitAttribute()
    {
        dd($this->inventories->pluck('potential_profit'));
        return $this->item_quantity * $this->markup_price;
    }

    public function getDisplayPotentialProfitAttribute()
    {
        return CurrencyHelper::formatCurrency($this->total_potential_profit);
    }

    public function getFullJsonResponseArray()
    {
        return $this->only([
            'name',
            'store_code',
            'this_user_main_store',
        ]);
    }

}
