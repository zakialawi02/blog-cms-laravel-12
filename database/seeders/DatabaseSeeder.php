<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ArticleView;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(WebSettings::class);
        $this->call(Users::class);

        // User::factory(200)->create();
        $this->call(CategorySeeder::class);
        // $this->call(TagSeeder::class);
        // $this->call(ArticleSeeder::class);
        // ArticleView::factory(500)->create();
        $this->call(PagesSeeder::class);

        $this->call(MenuSeeder::class);
    }
}
