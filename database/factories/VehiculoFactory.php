<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VehiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $true=1;
        return [
                "marca"=>$this->faker->word(),
                "modelo"=>$this->faker->title(),
                "public_id"=>$this->faker->numberBetween(1,1000),
                "url"=>$this->faker->imageUrl(640, 480, 'animals', true),
                "estado"=>$true,
        ];
    }
}
