<?php

namespace App\Models;

use App\Events\StoresCreatingEvent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model {
    use HasFactory;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_stores');
    }

    public function managerUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_stores')
            ->wherePivot('store_manager', true);
    }

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

}
