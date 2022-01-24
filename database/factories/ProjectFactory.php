<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $invite_link_status = $this->faker->boolean();
        return [
            // 'user_id'   => rand(1, 2),
            'user_id'   => 1,
            'title'     => $this->faker->text(20),
            'description'   => $this->faker->text(200),
            'invite_link_token' => $invite_link_status ? Str::random(32) : null,
            'invite_link_status' => $invite_link_status,
        ];
    }

}
