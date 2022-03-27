<?php

use App\Models\Store;
use App\Models\User;

class UserTest extends TestCase
{
    private $user;

    public function setUp():void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function testUserRelationshipStores()
    {
        $store = Store::factory()->create();
        $this->user->stores()->attach($store->id);

        $userStores = $this->user->stores;
        $this->assertCount(1, $userStores);
        $this->assertEquals($store->id, $userStores->first()->id);
    }

    public function testUserRelationshipManagingStores()
    {
        $store = Store::factory()->create();
        $this->user->stores()->attach($store->id, [
            'store_manager' => true,
        ]);

        $userStores = $this->user->managingStores;
        $this->assertCount(1, $userStores);
        $this->assertEquals($store->id, $userStores->first()->id);
    }

    public function testUserRelationshipManagingStoresForNonManagerRelation()
    {
        $store = Store::factory()->create();
        $this->user->stores()->attach($store->id, [
            'store_manager' => false,
        ]);

        $userStores = $this->user->managingStores;
        $this->assertCount(0, $userStores);
    }
}
