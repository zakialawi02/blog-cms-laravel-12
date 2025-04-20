<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ArticleService;

class HomeController extends Controller
{
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    public function index(Request $request)
    {
        $data = [
            'title' => env('APP_NAME')
        ];

        $articles = $this->articleService->fetchArticles();
        $this->articleService->articlesMappingArray($articles);
        $featured = $this->articleService->getFeaturedArticles($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);

        return view('pages.front.indexHome', compact('data', 'articles', 'featured', 'randomPosts'));
    }
}
