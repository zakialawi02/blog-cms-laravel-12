<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = [
            [
                'name' => "Main Header",
                'location' => "header",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => "Header Top",
                'location' => "header-top",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => "Footer Menu 1",
                'location' => "footer-a",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => "Footer Menu 2",
                'location' => "footer-b",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Menu::insert($menus);

        $itemMenus = [
            [
                'menu' => 1,
                'label' => "Home",
                'link' => "/",
                'sort' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 1,
                'label' => "Programming",
                'link' => "/blog/categories/programming",
                'sort' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 1,
                'label' => "Technology",
                'link' => "/blog/categories/technology",
                'sort' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 2,
                'label' => "Home",
                'link' => "/",
                'sort' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 2,
                'label' => "About",
                'link' => "/about",
                'sort' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 2,
                'label' => "Contact",
                'link' => "/contact",
                'sort' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 3,
                'label' => "Home",
                'link' => "/",
                'sort' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 3,
                'label' => "Menu 1",
                'link' => "#",
                'sort' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 3,
                'label' => "Menu 2",
                'link' => "#",
                'sort' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'menu' => 4,
                'label' => "Menu 1",
                'link' => "#",
                'sort' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        MenuItem::insert($itemMenus);
    }
}
