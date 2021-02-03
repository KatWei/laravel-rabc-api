<?php

namespace Database\Seeders;

use App\Models\AdminUser;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        AdminUser::factory()->count(2)->create();
        $user = AdminUser::find(1);
        $user->name = 'admin';
        $user->username = 'peryiqiao';
        $user->save();
    }
}
