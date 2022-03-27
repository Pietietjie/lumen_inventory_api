<?php

namespace Database\Factories;

use App\Models\Store;
use Illuminate\Database\Eloquent\Factories\Factory;

class StoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Store::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // This format will generate store codes as in the following examples
        // "S00001" if id = 1
        // "S99999" if id = 99999
        // "S100000" if id = 100000
        $storeCode = sprintf('S%\'.05d', Store::count() + 1);
        return [
            'name'       => $this->faker->word,
            'store_code' => $storeCode,
        ];
    }
}
