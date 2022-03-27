<?php

namespace Database\Factories;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Inventory::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        if (! Item::exists()) {
            Item::factory()->create();
        }
        if (! Store::exists()) {
            Store::factory()->create();
        }
        $item = Item::inRandomOrder()->first();
        return [
            'item_id'           => $item->id,
            'store_id'          => Store::inRandomOrder()->first()->id,
            'item_quantity'     => $this->faker->numberBetween(10, 10000),
            'store_item_price'  => $this->faker->numberBetween($item->default_price, 5000),
            'store_item_markup' => $this->faker->numberBetween($item->default_markup, 100),
        ];
    }
}
