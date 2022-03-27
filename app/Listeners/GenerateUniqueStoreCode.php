<?php

namespace App\Listeners;

use App\Events\StoresCreatingEvent;
use App\Models\Store;

class GenerateUniqueStoreCode
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     * Generates a simple unique store code based on the store ID
     *
     * @param  \App\Events\StoresCreatingEvent  $event
     * @return void
     */
    public function handle(StoresCreatingEvent $event)
    {
        // This format will generate store codes as in the following examples
        // "S00001" if id = 1
        // "S99999" if id = 99999
        // "S100000" if id = 100000
        $storeCode = sprintf('S%\'.05d', Store::withTrashed()->count() + 1);

        if (! Store::where('store_code', $storeCode)->exists()) {
            $event->store->store_code = $storeCode;
            return true;
        }
        return false;
    }
}
