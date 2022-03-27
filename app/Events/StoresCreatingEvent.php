<?php

namespace App\Events;

use App\Models\Store;
use Illuminate\Broadcasting\PrivateChannel;

class StoresCreatingEvent extends Event
{
    public $store;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }
}
