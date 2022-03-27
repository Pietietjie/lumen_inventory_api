<?php

use App\Models\Inventory;
use App\Models\Store;
use App\Models\User;

class StoreTest extends TestCase
{
    private $store;

    public function setUp():void
    {
        parent::setUp();
        $this->store = Store::factory()->create();
    }

    public function testStoreRelationshipInventories()
    {
        $inventory = Inventory::factory()->create([
            'store_id' => $this->store->id,
        ]);

        $storeInventories = $this->store->inventories;
        $this->assertCount(1, $storeInventories);
        $this->assertEquals($inventory->id, $storeInventories->first()->id);
    }

    public function testStoreRelationshipUsers()
    {
        $user = User::factory()->create();
        $this->store->users()->attach($user->id);

        $storeUsers = $this->store->users;
        $this->assertCount(1, $storeUsers);
        $this->assertEquals($user->id, $storeUsers->first()->id);
    }

    public function testStoreRelationshipManagerUsers()
    {
        $user = User::factory()->create();
        $this->store->users()->attach($user->id, [
            'store_manager' => true,
        ]);

        $storeUsers = $this->store->managerUsers;
        $this->assertCount(1, $storeUsers);
        $this->assertEquals($user->id, $storeUsers->first()->id);
    }

    public function testStoreRelationshipManagerUsersForNonManagerRelation()
    {
        $user = User::factory()->create();
        $this->store->users()->attach($user->id, [
            'store_manager' => false,
        ]);

        $storeUsers = $this->store->managerUsers;
        $this->assertCount(0, $storeUsers);
    }

    public function testStoreEventGenerateUniqueStoreCode()
    {
        $newStore = Store::factory()->create();

        $newStoreNonFactory = new Store;
        $newStoreNonFactory->name = 'non factory';
        $newStoreNonFactory->save();

        $this->assertNotEquals($this->store->store_code, $newStore->store_code);
        $this->assertNotEquals($this->store->store_code, $newStoreNonFactory->store_code);
    }
}
