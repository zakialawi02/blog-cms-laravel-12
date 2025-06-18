<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SectionContentService;

class HomeController extends Controller
{
    protected $sectionContentService;

    public function __construct(SectionContentService $sectionContentService)
    {
        $this->sectionContentService = $sectionContentService;
    }

    public function index(Request $request)
    {
        $data = [
            'title' => env('APP_NAME')
        ];

        $sectionsContent = $this->sectionContentService->getSectionData();

        return view('pages.front.indexHome', compact('data', 'sectionsContent'));
    }
}
