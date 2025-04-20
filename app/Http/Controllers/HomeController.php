<?php

namespace App\Http\Controllers;

use App\Models\Tag;
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

        $articles = $this->articleService->fetchArticles(['per_page' => 6]);
        $this->articleService->articlesMappingArray($articles);
        $popularPosts = $this->articleService->getPopularPosts(4);
        $featured = $this->articleService->getFeaturedArticles($articles);
        $randomPosts = $this->articleService->getRandomArticles(4);
        $tags = Tag::inRandomOrder()->limit(10)->get();

        return view('pages.front.indexHome', compact('data', 'articles', 'popularPosts', 'featured', 'randomPosts', 'tags'));
    }
}
