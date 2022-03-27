<?php

use App\Models\Inventory;
use App\Models\Item;
use Illuminate\Support\Facades\Config;

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

    public function testInventoryAttributeDefaultMarkupPrice()
    {
        $this->item->default_price  = 100;
        $this->item->default_markup = .1;
        $this->item->save();

        $this->assertEquals(10, $this->item->default_markup_price);
    }

    public function testInventoryAttributeDisplayDefaultMarkupPrice()
    {
        Config::set('currency.symbol', 'R');
        $this->item->default_price  = 100;
        $this->item->default_markup = .1;
        $this->item->save();

        $this->assertEquals('R10.00', $this->item->display_default_markup_price);
    }

    public function testInventoryAttributeDisplayDefaultMarkupPriceUSD()
    {
        Config::set('currency.symbol', '$');
        $this->item->default_price  = 100;
        $this->item->default_markup = .1;
        $this->item->save();

        $this->assertEquals('$10.00', $this->item->display_default_markup_price);
    }

    public function testInventoryAttributeDisplayDefaultMarkupPriceWithCents()
    {
        Config::set('currency.symbol', 'R');
        $this->item->default_price  = 100;
        $this->item->default_markup = .001;
        $this->item->save();

        $this->assertEquals('R0.10', $this->item->display_default_markup_price);
    }

    public function testInventoryAttributeDisplayDefaultMarkupPriceWithThousands()
    {
        Config::set('currency.symbol', 'R');
        $this->item->default_price  = 100;
        $this->item->default_markup = 10;
        $this->item->save();

        $this->assertEquals('R1 000.00', $this->item->display_default_markup_price);
    }

    public function testInventoryAttributeDefaultSellPrice()
    {
        $this->item->default_price  = 100;
        $this->item->default_markup = .1;
        $this->item->save();

        $this->assertEquals(110, $this->item->default_sell_price);
    }

    public function testInventoryAttributeDisplayDefaultSellPrice()
    {
        Config::set('currency.symbol', 'R');
        $this->item->default_price  = 100;
        $this->item->default_markup = .1;
        $this->item->save();

        $this->assertEquals('R110.00', $this->item->display_default_sell_price);
    }

    public function testInventoryAttributeDisplayDefaultSellPriceUSD()
    {
        Config::set('currency.symbol', '$');
        $this->item->default_price  = 100;
        $this->item->default_markup = .1;
        $this->item->save();

        $this->assertEquals('$110.00', $this->item->display_default_sell_price);
    }

    public function testInventoryAttributeDisplayDefaultSellPriceWithCents()
    {
        Config::set('currency.symbol', 'R');
        $this->item->default_price  = 100;
        $this->item->default_markup = .001;
        $this->item->save();

        $this->assertEquals('R100.10', $this->item->display_default_sell_price);
    }

    public function testInventoryAttributeDisplayDefaultSellPriceWithThousands()
    {
        Config::set('currency.symbol', 'R');
        $this->item->default_price  = 100;
        $this->item->default_markup = 10;
        $this->item->save();

        $this->assertEquals('R1 100.00', $this->item->display_default_sell_price);
    }

}
