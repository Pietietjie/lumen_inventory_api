<?php

use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;

class ItemControllerTest extends TestCase
{
    private $user;

    public function setUp():void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    public function testItemControllerCreate()
    {
        $response = $this->post(route('item.add'), [
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 200,
            'default_markup' => .5,
        ]);
        $response->seeJson([
            'item' => [
                'name'                         => 'Test product',
                'ean13_bar_code'               => '9999999999999',
                'default_price'                => 200,
                'default_markup'               => .5,
                'default_markup_price'         => 100,
                'default_sell_price'           => 300,
                'display_default_markup_price' => 'R100.00',
                'display_default_sell_price'   => 'R300.00',
            ],
            'message' => 'Item Added',
        ]);
    }

    public function testItemControllerCreateSoftDeletedItem()
    {
        $item = Item::factory()->create([
            'name'           => 'Deleted product',
            'ean13_bar_code' => '9999999999999',
        ]);
        $item->delete();

        $response = $this->post(route('item.add'), [
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 200,
            'default_markup' => .5,
        ]);
        $response->seeJson([
            'item' => [
                'name'                         => 'Test product',
                'ean13_bar_code'               => '9999999999999',
                'default_price'                => 200,
                'default_markup'               => .5,
                'default_markup_price'         => 100,
                'default_sell_price'           => 300,
                'display_default_markup_price' => 'R100.00',
                'display_default_sell_price'   => 'R300.00',
            ],
            'message' => 'Item Added',
        ]);

        $restoredItem = Item::find($item->id);
        $this->assertEquals($item->ean13_bar_code, $restoredItem->ean13_bar_code);
        $this->assertEquals('Test product', $restoredItem->name);
    }

    public function testItemControllerCreateEmptyRequest()
    {
        $response = $this->post(route('item.add'), []);
        $response->seeJson([
            'name'           => ['The name field is required.'],
            'default_markup' => ['The default markup field is required.'],
            'default_price'  => ['The default price field is required.'],
            'ean13_bar_code' => ['The ean13 bar code field is required.'],
        ]);
    }

    public function testItemControllerCreateInvalidRequest()
    {
        $response = $this->post(route('item.add'), [
            'name'           => 'O',
            'ean13_bar_code' => 'not a barcode',
            'default_price'  => 'cheap',
            'default_markup' => 'too much',
        ]);
        $response->seeJson([
            'name'           => ['The name must be between 2 and 100 characters.'],
            'default_markup' => ['The default markup must be a number.'],
            'default_price'  => ['The default price must be a number.'],
            'ean13_bar_code' => [
                "The ean13 bar code must be 13 digits.",
                "The ean13 bar code must be a number."
            ],
        ]);
    }

    public function testItemControllerUpdateItem()
    {
        $item = Item::factory()->create([
            'name'           => 'Original product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);

        $response = $this->put(route('item.update'), [
            'name'           => 'Test product',
            'ean13_bar_code' => $item->ean13_bar_code,
            'default_price'  => 200,
            'default_markup' => .5,
        ]);
        $response->seeJson([
            'item' => [
                'name'                         => 'Test product',
                'ean13_bar_code'               => '9999999999999',
                'default_price'                => 200,
                'default_markup'               => .5,
                'default_markup_price'         => 100,
                'default_sell_price'           => 300,
                'display_default_markup_price' => 'R100.00',
                'display_default_sell_price'   => 'R300.00',
            ],
            'message' => 'Item Updated',
        ]);

        $updatedItem = Item::find($item->id);
        $this->assertEquals($item->ean13_bar_code, $updatedItem->ean13_bar_code);
        $this->assertEquals('Test product', $updatedItem->name);
        $this->assertEquals(200, $updatedItem->default_price);
        $this->assertEquals(.5, $updatedItem->default_markup);
    }

    public function testItemControllerUpdateSoftDeletedItem()
    {
        $item = Item::factory()->create([
            'name'           => 'Original product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);
        $item->delete();

        $response = $this->put(route('item.update'), [
            'name'           => 'Test product',
            'ean13_bar_code' => $item->ean13_bar_code,
            'default_price'  => 200,
            'default_markup' => .5,
        ]);
        $response->seeJson([
            'ean13_bar_code' => ['The selected ean13 bar code is invalid.'],
        ]);
    }
    
    public function testItemControllerUpdateEmptyRequest()
    {
        $response = $this->put(route('item.update'), []);
        $response->seeJson([
            'ean13_bar_code' => ['The ean13 bar code field is required.'],
        ]);
    }

    public function testItemControllerUpdateInvalidRequest()
    {
        $response = $this->put(route('item.update'), [
            'name'           => 'O',
            'ean13_bar_code' => 'not a barcode',
            'default_price'  => 'cheap',
            'default_markup' => 'too much',
        ]);
        $response->seeJson([
            'name'           => ['The name must be between 2 and 100 characters.'],
            'default_markup' => ['The default markup must be a number.'],
            'default_price'  => ['The default price must be a number.'],
            'ean13_bar_code' => [
                "The ean13 bar code must be 13 digits.",
                "The ean13 bar code must be a number."
            ],
        ]);
    }

    public function testItemControllerDeleteItem()
    {
        $item = Item::factory()->create([
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);

        $response = $this->delete(route('item.delete'), [
            'ean13_bar_code' => $item->ean13_bar_code,
        ]);
        $response->seeJson([
            'message' => 'Item Deleted',
        ]);
        $deletedItem = Item::onlyTrashed()->find($item->id);
        $this->assertNotNull($deletedItem);
        $this->assertEquals($item->id, $deletedItem->id);

    }

    public function testItemControllerDeleteItemWithInventories()
    {
        $item = Item::factory()->create([
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);

        $inventories = Inventory::factory(2)->create([
            'item_id' => $item->id,
        ]);

        $response = $this->delete(route('item.delete'), [
            'ean13_bar_code' => $item->ean13_bar_code,
        ]);
        $response->seeJson([
            'message' => 'Item Deleted',
        ]);
        $deletedItem = Item::onlyTrashed()->find($item->id);
        $this->assertNotNull($deletedItem);
        $this->assertEquals($item->id, $deletedItem->id);

        $deletedInventories = Inventory::onlyTrashed()->whereIn('id', $inventories->pluck('id'))->get();
        $this->assertCount(2, $deletedInventories);
    }

    public function testItemControllerDeleteAlreadyDeletedItem()
    {
        $item = Item::factory()->create([
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);
        $item->delete();

        $response = $this->delete(route('item.delete'), [
            'ean13_bar_code' => $item->ean13_bar_code,
        ]);
        $response->seeJson([
            'ean13_bar_code' => ['The selected ean13 bar code is invalid.'],
        ]);
    }

    public function testItemControllerDeleteNonExistingItem()
    {
        $response = $this->delete(route('item.delete'), [
            'ean13_bar_code' => '9999999999999',
        ]);
        $response->seeJson([
            'ean13_bar_code' => ['The selected ean13 bar code is invalid.'],
        ]);
    }

    public function testItemControllerDeleteEmptyRequest()
    {
        $response = $this->delete(route('item.delete'), []);
        $response->seeJson([
            'ean13_bar_code' => ['The ean13 bar code field is required.'],
        ]);
    }

    public function testItemControllerDeleteInvalidRequest()
    {
        $response = $this->delete(route('item.delete'), [
            'ean13_bar_code' => 'not a barcode',
        ]);
        $response->seeJson([
            'ean13_bar_code' => [
                "The ean13 bar code must be 13 digits.",
                "The ean13 bar code must be a number."
            ],
        ]);
    }

    public function testItemControllerRestoreItem()
    {
        $item = Item::factory()->create([
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);
        $item->delete();

        $response = $this->post(route('item.restore'), [
            'ean13_bar_code' => $item->ean13_bar_code,
        ]);
        $response->seeJson([
            'message' => 'Item Restored',
        ]);
        $restoredItem = Item::find($item->id);
        $this->assertNotNull($restoredItem);
        $this->assertEquals($item->id, $restoredItem->id);

    }

    public function testItemControllerRestoreItemWithInventories()
    {
        $item = Item::factory()->create([
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);
        $item->delete();

        
        $inventories = Inventory::factory(2)->create([
            'item_id' => $item->id,
        ]);
        $item->inventories()->delete();

        $response = $this->post(route('item.restore'), [
            'ean13_bar_code' => $item->ean13_bar_code,
        ]);
        $response->seeJson([
            'message' => 'Item Restored',
        ]);
        $restoredItem = Item::find($item->id);
        $this->assertNotNull($restoredItem);
        $this->assertEquals($item->id, $restoredItem->id);

        $restoredInventories = Inventory::whereIn('id', $inventories->pluck('id'))->get();
        $this->assertCount(2, $restoredInventories);
    }

    public function testItemControllerRestoreAlreadyExistingItem()
    {
        $item = Item::factory()->create([
            'name'           => 'Test product',
            'ean13_bar_code' => '9999999999999',
            'default_price'  => 100,
            'default_markup' => .25,
        ]);

        $response = $this->post(route('item.restore'), [
            'ean13_bar_code' => $item->ean13_bar_code,
        ]);
        $response->seeJson([
            'ean13_bar_code' => ['The selected ean13 bar code is invalid.'],
        ]);
    }

    public function testItemControllerRestoreNonExistingItem()
    {
        $response = $this->post(route('item.restore'), [
            'ean13_bar_code' => '9999999999999',
        ]);
        $response->seeJson([
            'ean13_bar_code' => ['The selected ean13 bar code is invalid.'],
        ]);
    }

    public function testItemControllerRestoreEmptyRequest()
    {
        $response = $this->post(route('item.restore'), []);
        $response->seeJson([
            'ean13_bar_code' => ['The ean13 bar code field is required.'],
        ]);
    }

    public function testItemControllerRestoreInvalidRequest()
    {
        $response = $this->post(route('item.restore'), [
            'ean13_bar_code' => 'not a barcode',
        ]);
        $response->seeJson([
            'ean13_bar_code' => [
                "The ean13 bar code must be 13 digits.",
                "The ean13 bar code must be a number."
            ],
        ]);
    }
}
