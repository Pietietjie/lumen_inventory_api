<?php

namespace App\Models;

use App\Events\StoresCreatingEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model {
    use HasFactory, SoftDeletes;

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

}
