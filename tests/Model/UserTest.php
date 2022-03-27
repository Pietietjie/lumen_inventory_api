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

    public function testUserRelationshipMainStore()
    {
        $store = Store::factory()->create();
        $this->user->stores()->attach($store->id, [
            'user_main_store' => true,
        ]);

        $userStores = $this->user->mainStore;
        $this->assertCount(1, $userStores);
        $this->assertEquals($store->id, $userStores->first()->id);
    }

    public function testUserRelationshipMainStoreForNonManagerRelation()
    {
        $store = Store::factory()->create();
        $this->user->stores()->attach($store->id, [
            'user_main_store' => false,
        ]);

        $userStores = $this->user->mainStore;
        $this->assertCount(0, $userStores);
    }
}
