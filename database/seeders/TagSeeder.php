<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            [
                'tag_name' => "Introduction",
                'slug' => "introduction",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tag_name' => "Code",
                'slug' => "code",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'tag_name' => "Tips",
                'slug' => "tips",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Tag::insert($tags);
    }
}
