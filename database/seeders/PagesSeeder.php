<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'title' => "Contact",
                'description' => "Page of Contact",
                'slug' => "contact",
                'content' => '{ "assets": [], "styles": [ { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "display": "flex", "justify-content": "flex-start", "align-items": "stretch", "flex-wrap": "nowrap", "padding-top": "10px", "padding-right": "10px", "padding-bottom": "10px", "padding-left": "10px" } }, { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "flex-wrap": "wrap" }, "mediaText": "(max-width: 768px)", "atRuleType": "media" }, { "selectors": [{ "name": "gjs-cell", "private": 1 }], "style": { "min-height": "75px", "flex-grow": "1", "flex-basis": "100%" } } ], "pages": [ { "frames": [ { "component": { "type": "wrapper", "stylable": [ "background", "background-color", "background-image", "background-repeat", "background-attachment", "background-position", "background-size" ], "attributes": { "id": "iq2q" } }, "id": "QPjKujkHrih5afuF" } ], "id": "MtEglcsgDUdI00Ua" } ] }',
                'isFullWidth' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Privacy Policy",
                'description' => "Page of Privacy",
                'slug' => "privacy",
                'content' => '{ "assets": [], "styles": [ { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "display": "flex", "justify-content": "flex-start", "align-items": "stretch", "flex-wrap": "nowrap", "padding-top": "10px", "padding-right": "10px", "padding-bottom": "10px", "padding-left": "10px" } }, { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "flex-wrap": "wrap" }, "mediaText": "(max-width: 768px)", "atRuleType": "media" }, { "selectors": [{ "name": "gjs-cell", "private": 1 }], "style": { "min-height": "75px", "flex-grow": "1", "flex-basis": "100%" } } ], "pages": [ { "frames": [ { "component": { "type": "wrapper", "stylable": [ "background", "background-color", "background-image", "background-repeat", "background-attachment", "background-position", "background-size" ], "attributes": { "id": "iq2q" } }, "id": "QPjKujkHrih5afuF" } ], "id": "MtEglcsgDUdI00Ua" } ] }',
                'isFullWidth' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => "Term of Conditions",
                'description' => "Page of Terms",
                'slug' => "terms",
                'content' => '{ "assets": [], "styles": [ { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "display": "flex", "justify-content": "flex-start", "align-items": "stretch", "flex-wrap": "nowrap", "padding-top": "10px", "padding-right": "10px", "padding-bottom": "10px", "padding-left": "10px" } }, { "selectors": [{ "name": "gjs-row", "private": 1 }], "style": { "flex-wrap": "wrap" }, "mediaText": "(max-width: 768px)", "atRuleType": "media" }, { "selectors": [{ "name": "gjs-cell", "private": 1 }], "style": { "min-height": "75px", "flex-grow": "1", "flex-basis": "100%" } } ], "pages": [ { "frames": [ { "component": { "type": "wrapper", "stylable": [ "background", "background-color", "background-image", "background-repeat", "background-attachment", "background-position", "background-size" ], "attributes": { "id": "iq2q" } }, "id": "QPjKujkHrih5afuF" } ], "id": "MtEglcsgDUdI00Ua" } ] }',
                'isFullWidth' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($pages as $page) {
            Page::create($page);
        }
    }
}
