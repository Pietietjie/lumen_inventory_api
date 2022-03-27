<?php

use App\Models\Inventory;
use App\Models\Item;

class ItemTest extends TestCase
{
    private $item;

    public function setUp():void
    {
        parent::setUp();
        $this->item = Item::factory()->create();
    }

    public function testItemRelationshipInventories()
    {
        $inventory = Inventory::factory()->create([
            'item_id' => $this->item->id,
        ]);

        $itemInventories = $this->item->inventories;
        $this->assertCount(1, $itemInventories);
        $this->assertEquals($inventory->id, $itemInventories->first()->id);
    }

}
