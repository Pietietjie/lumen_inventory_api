<?php

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Store;

class InventoryTest extends TestCase
{
    private $inventory;

    public function setUp():void
    {
        parent::setUp();
        
        $this->inventory = Inventory::factory()->create();
    }

    public function testInventoryRelationItem()
    {
        $item = Item::factory()->create();
        $this->inventory->item_id = $item->id;
        $this->inventory->save();

        $inventoryItem = $this->inventory->item;
        $this->assertNotNull($inventoryItem);
        $this->assertEquals($item->id, $inventoryItem->id);
    }

    public function testInventoryRelationStore()
    {
        $store = Store::factory()->create();
        $this->inventory->store_id = $store->id;
        $this->inventory->save();

        $inventoryStore = $this->inventory->store;
        $this->assertNotNull($inventoryStore);
        $this->assertEquals($store->id, $inventoryStore->id);
    }
}
