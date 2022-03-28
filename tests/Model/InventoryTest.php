<?php

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Support\Facades\Config;

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

    public function testInventoryAttributeMarkupPrice()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals(15, $this->inventory->markup_price);
    }

    public function testInventoryAttributeMarkupPriceInventoryMarkup()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = .25;
        $this->inventory->save();

        $this->assertEquals(25, $this->inventory->markup_price);
    }

    public function testInventoryAttributeMarkupPriceInventoryMarkupPrice()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = 200;
        $this->inventory->store_item_markup = .25;
        $this->inventory->save();

        $this->assertEquals(50, $this->inventory->markup_price);
    }

    public function testInventoryAttributeDisplayMarkupPrice()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('R15.00', $this->inventory->display_markup_price);
    }

    public function testInventoryAttributeDisplayMarkupPriceUSD()
    {
        Config::set('currency.symbol', '$');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('$15.00', $this->inventory->display_markup_price);
    }

    public function testInventoryAttributeDisplayMarkupPriceWithCents()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 10,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('R1.50', $this->inventory->display_markup_price);
    }

    public function testInventoryAttributeDisplayMarkupPriceWithThousands()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 10000,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('R1 500.00', $this->inventory->display_markup_price);
    }

    public function testInventoryAttributeDisplayMarkupPriceInventoryMarkup()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 10000,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = .25;
        $this->inventory->save();

        $this->assertEquals('R2 500.00', $this->inventory->display_markup_price);
    }

    public function testInventoryAttributeDisplayMarkupPriceInventoryPrice()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 10000,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = 20000;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('R3 000.00', $this->inventory->display_markup_price);
    }

    public function testInventoryAttributeSellPrice()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals(115, $this->inventory->sell_price);
    }

    public function testInventoryAttributeSellPriceInventoryMarkup()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = .25;
        $this->inventory->save();

        $this->assertEquals(125, $this->inventory->sell_price);
    }

    public function testInventoryAttributeSellPriceInventoryPrice()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = 200;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals(230, $this->inventory->sell_price);
    }

    public function testInventoryAttributeDisplaySellPrice()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('R115.00', $this->inventory->display_sell_price);
    }

    public function testInventoryAttributeDisplaySellPriceUSD()
    {
        Config::set('currency.symbol', '$');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('$115.00', $this->inventory->display_sell_price);
    }

    public function testInventoryAttributeDisplaySellPriceWithCents()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .015,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('R101.50', $this->inventory->display_sell_price);
    }

    public function testInventoryAttributeDisplaySellPriceWithThousands()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 1000,
            'default_markup' => .015,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->save();

        $this->assertEquals('R1 015.00', $this->inventory->display_sell_price);
    }

    public function testInventoryAttributePotentialProfit()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->item_quantity     = 10;
        $this->inventory->save();

        $this->assertEquals(150, $this->inventory->potential_profit);
    }

    public function testInventoryAttributePotentialProfitNoItems()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->item_quantity     = 0;
        $this->inventory->save();

        $this->assertEquals(0, $this->inventory->potential_profit);
    }

    public function testInventoryAttributePotentialProfitInventoryMarkup()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = .25;
        $this->inventory->item_quantity     = 10;
        $this->inventory->save();

        $this->assertEquals(250, $this->inventory->potential_profit);
    }

    public function testInventoryAttributePotentialProfitInventoryPrice()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = 200;
        $this->inventory->store_item_markup = null;
        $this->inventory->item_quantity     = 10;
        $this->inventory->save();

        $this->assertEquals(300, $this->inventory->potential_profit);
    }

    public function testInventoryAttributeDisplayPotentialProfit()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->item_quantity     = 10;
        $this->inventory->save();

        $this->assertEquals('R150.00', $this->inventory->display_potential_profit);
    }

    public function testInventoryAttributeDisplayPotentialProfitUSD()
    {
        Config::set('currency.symbol', '$');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->item_quantity     = 10;
        $this->inventory->save();

        $this->assertEquals('$150.00', $this->inventory->display_potential_profit);
    }

    public function testInventoryAttributeDisplayPotentialProfitWithCents()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .0015,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->item_quantity     = 10;
        $this->inventory->save();

        $this->assertEquals('R1.50', $this->inventory->display_potential_profit);
    }

    public function testInventoryAttributeDisplayPotentialProfitWithThousands()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 100,
            'default_markup' => .15,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->store_item_markup = null;
        $this->inventory->item_quantity     = 1000;
        $this->inventory->save();

        $this->assertEquals('R15 000.00', $this->inventory->display_potential_profit);
    }

    public function testInventoryAttributePurchasePrice()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->save();

        $this->assertEquals(100, $this->inventory->purchase_price);
    }

    public function testInventoryAttributePurchasePriceInventoryPrice()
    {
        $item = Item::factory()->create([
            'default_price'  => 100,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = 150;
        $this->inventory->save();

        $this->assertEquals(150, $this->inventory->purchase_price);
    }

    public function testInventoryAttributeDisplayPurchasePrice()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 100,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->save();

        $this->assertEquals('R100.00', $this->inventory->display_purchase_price);
    }

    public function testInventoryAttributeDisplayPurchasePriceUSD()
    {
        Config::set('currency.symbol', '$');
        $item = Item::factory()->create([
            'default_price'  => 100,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->save();

        $this->assertEquals('$100.00', $this->inventory->display_purchase_price);
    }

    public function testInventoryAttributeDisplayPurchasePriceWithCents()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 99.9,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->save();

        $this->assertEquals('R99.90', $this->inventory->display_purchase_price);
    }

    public function testInventoryAttributeDisplayPurchasePriceWithThousands()
    {
        Config::set('currency.symbol', 'R');
        $item = Item::factory()->create([
            'default_price'  => 1001000,
        ]);
        $this->inventory->item_id           = $item->id;
        $this->inventory->store_item_price  = null;
        $this->inventory->save();

        $this->assertEquals('R1 001 000.00', $this->inventory->display_purchase_price);
    }

}
