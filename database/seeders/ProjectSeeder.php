<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::factory()->times(5)
            ->has(
                ProjectMember::factory()
                    ->state(function (array $attributes, Project $project) {
                        return [
                            'user_id'   => $project->user_id,
                            'project_id'=> $project->id,
                            'email'     => $project->user->email,
                            'is_admin'  => true,
                        ];
                    })
            , 'members')
            ->create();
    }
}
