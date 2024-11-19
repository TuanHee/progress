<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProjectMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // $project_id =
        $user = User::find(rand(1, User::count()));

        return [
            'user_id'   => $user->id,
            'project_id' => rand(1, Project::count()),
            'email'     => $user->email,
            'invite_token'  => null,
            'is_admin'  => $this->faker->boolean(),
            'validated_at'  => now(),
        ];
    }
}
