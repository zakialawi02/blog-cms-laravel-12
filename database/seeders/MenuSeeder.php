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
                'name' => 'Main Header',
                'location' => 'header',
            ],
            [
                'name' => 'Header Top',
                'location' => 'header-top',
            ],
            [
                'name' => 'Footer Menu 1',
                'location' => 'footer-a',
            ],
            [
                'name' => 'Footer Menu 2',
                'location' => 'footer-b',
            ],
        ];

        Menu::insert($menus);

        $itemMenus = [
            // Menu ID 1 (Main Header)
            [
                'label' => 'Home',
                'link' => '/',
                'parent' => null,
                'sort' => 0,
                'class' => null,
                'menu' => 1,
                'depth' => 0,
            ],
            [
                'label' => 'Programming',
                'link' => '/blog/categories/programming',
                'parent' => null,
                'sort' => 1,
                'class' => null,
                'menu' => 1,
                'depth' => 0,
            ],
            [
                'label' => 'Technology',
                'link' => '/blog/categories/technology',
                'parent' => null,
                'sort' => 2,
                'class' => null,
                'menu' => 1,
                'depth' => 0,
            ],

            // Menu ID 2 (Header Top)
            [
                'label' => 'Home',
                'link' => '/',
                'parent' => null,
                'sort' => 0,
                'class' => null,
                'menu' => 2,
                'depth' => 0,
            ],
            [
                'label' => 'About',
                'link' => '/about',
                'parent' => null,
                'sort' => 1,
                'class' => null,
                'menu' => 2,
                'depth' => 0,
            ],
            [
                'label' => 'Contact',
                'link' => '/contact',
                'parent' => null,
                'sort' => 2,
                'class' => null,
                'menu' => 2,
                'depth' => 0,
            ],

            // Menu ID 3 (Footer Menu 1)
            [
                'label' => 'Home',
                'link' => '/',
                'parent' => null,
                'sort' => 1,
                'class' => null,
                'menu' => 3,
                'depth' => 0,
            ],
            [
                'label' => 'Menu 1',
                'link' => '#',
                'parent' => null,
                'sort' => 2,
                'class' => null,
                'menu' => 3,
                'depth' => 0,
            ],
            [
                'label' => 'Menu 2',
                'link' => '#',
                'parent' => null,
                'sort' => 3,
                'class' => null,
                'menu' => 3,
                'depth' => 0,
            ],

            // Menu ID 4 (Footer Menu 2)
            [
                'label' => 'Menu 1',
                'link' => '#',
                'parent' => null,
                'sort' => 0,
                'class' => null,
                'menu' => 4,
                'depth' => 0,
            ],
        ];

        MenuItem::insert($itemMenus);
    }
}
