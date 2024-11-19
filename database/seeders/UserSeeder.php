<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->times(2)->create();

        User::where('id', 1)->update([
            'name'  => 'Project Admin',
            'email' => 'admin@example.com',
        ]);

        User::where('id', 2)->update([
            'name'  => 'User 1',
            'email' => 'user@example.com',
        ]);
    }
}
