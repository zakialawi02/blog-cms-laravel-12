<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Models\ArticleView;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\RequestContributor;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::user()->id;
        if (Auth::user()->role == 'superadmin' || Auth::user()->role == 'admin') {
            if ($request->ajax() && $request->has('ajax')) {
                $allPostsCount = Article::count();
                $allPostsPublishedQuery = Article::where('status', 'published')
                    ->where('published_at', '<', now());
                $allPostsPublishedCount = (clone $allPostsPublishedQuery)->count();
                $myPostsQuery = Article::where('user_id', $userId);
                $myPostsCount = (clone $myPostsQuery)->count();
                $myPostsPublishedCount = (clone $myPostsQuery)
                    ->where('status', 'published')
                    ->where('published_at', '<', now())
                    ->count();
                $allCommentsCount = Comment::count();
                $myCommentsCount = Comment::where('user_id', $userId)->count();
                $usersCount = User::count();
                $visitors = ArticleView::count();
                $viewsMyPosts = Article::where('user_id', $userId)->withCount('articleViews')->get()->sum('article_views_count');
                $latestAllPostsQuery = Article::with('user')->latest()->limit(5);
                $popularPostsQuery = Article::withCount('articleViews as total_views')
                    ->orderBy('total_views', 'desc')
                    ->limit(5);
                $latestAllPosts = $latestAllPostsQuery->get();
                $popularPosts = $popularPostsQuery->get();
                $recentComments = Comment::with(['user', 'article'])->latest()->limit(5)->get();

                return response()->json([
                    'success' => true,
                    'allPostsCount' => $allPostsCount,
                    'myPostsCount' => $myPostsCount,
                    'allPostsPublishedCount' => $allPostsPublishedCount,
                    'myPostsPublishedCount' => $myPostsPublishedCount,
                    'allCommentsCount' => $allCommentsCount,
                    'myCommentsCount' => $myCommentsCount,
                    'visitors' => $visitors,
                    'viewsMyPosts' => $viewsMyPosts,
                    'usersCount' => $usersCount,
                    'allposts' => $latestAllPosts,
                    'popularPosts' => $popularPosts,
                    'recentComment' => $recentComments,
                ], 200);
            }

            // data sent by ajax

            return view('pages.dashboard.dashboardAdmin');
        } elseif (Auth::user()->role == 'writer') {
            if ($request->ajax() && $request->has('ajax')) {
                $myPostsQuery = Article::where('user_id', $userId);
                $myPostsCount = (clone $myPostsQuery)->count();
                $myPostsPublishedCount = (clone $myPostsQuery)
                    ->where('status', 'published')
                    ->where('published_at', '<', now())
                    ->count();
                $myCommentsCount = Comment::where('user_id', $userId)->count();
                $viewsMyPosts = Article::where('user_id', $userId)->withCount('articleViews')->get()->sum('article_views_count');
                $latestAllPostsQuery = Article::with('user')
                    ->latest()->limit(5)->where('user_id', $userId);
                $popularPostsQuery = Article::withCount('articleViews as total_views')
                    ->orderBy('total_views', 'desc')->limit(5)->where('user_id', $userId);
                $latestAllPosts = $latestAllPostsQuery->get();
                $popularPosts = $popularPostsQuery->get();
                $recentComments = Comment::whereHas('article', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                })->with(['user', 'article'])->latest()->limit(5)->get();

                return response()->json([
                    'success' => true,
                    'myPostsCount' => $myPostsCount,
                    'myPostsPublishedCount' => $myPostsPublishedCount,
                    'myCommentsCount' => $myCommentsCount,
                    'viewsMyPosts' => $viewsMyPosts,
                    'allposts' => $latestAllPosts,
                    'popularPosts' => $popularPosts,
                    'recentComment' => $recentComments,
                ]);
            }

            // data sent by ajax

            return view('pages.dashboard.dashboard');
        } elseif (Auth::user()->role == 'user') {
            if ($request->ajax() && $request->has('ajax')) {
                $myCommentsCount = Comment::where('user_id', $userId)->count();
                $recentComments = Comment::where('user_id', $userId)
                    ->with(['user', 'article'])->latest()->limit(5)
                    ->get();

                return response()->json([
                    'success' => true,
                    'myCommentsCount' => $myCommentsCount,
                    'recentComment' => $recentComments,
                ]);
            }
            // data sent by ajax

            $myRequest = RequestContributor::where('user_id', Auth::id())->latest('created_at')->first();

            return view('pages.dashboard.dashboardUser', compact('myRequest'));
        } else {
            return view('pages.dashboard.emptyDashboard');
        }
    }

    public function info()
    {
        $data = [
            'title' => 'Application Information',
            'APP_NAME' => env('APP_NAME'),
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'APP_URL' => env('APP_URL'),
            'LOG_CHANNEL' => env('LOG_CHANNEL'),
            'LOG_LEVEL' => env('LOG_LEVEL'),
            'DB_CONNECTION' => env('DB_CONNECTION'),
            'DB_HOST' => env('DB_HOST'),
            'DB_PORT' => env('DB_PORT'),
            'DB_DATABASE' => env('DB_DATABASE'),
            'DB_USERNAME' => env('DB_USERNAME'),
            'CACHE_DRIVER' => env('CACHE_DRIVER'),
            'QUEUE_CONNECTION' => env('QUEUE_CONNECTION'),
            'SESSION_DRIVER' => env('SESSION_DRIVER'),
            'MAIL_MAILER' => env('MAIL_MAILER'),
            'MAIL_HOST' => env('MAIL_HOST'),
            'MAIL_PORT' => env('MAIL_PORT'),
            'MAIL_USERNAME' => env('MAIL_USERNAME'),
            'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
        ];

        $phpInfo = [
            'PHP Version' => phpversion(),
            'Zend Version' => zend_version(),
            'OS' => PHP_OS,
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? php_sapi_name(),
            'Loaded Extensions' => get_loaded_extensions(),
            'Memory Limit' => ini_get('memory_limit'),
            'Max Execution Time' => ini_get('max_execution_time'),
            'Upload Max Filesize' => ini_get('upload_max_filesize'),
            'Post Max Size' => ini_get('post_max_size'),
            'Display Errors' => ini_get('display_errors'),
            'Error Reporting' => ini_get('error_reporting'),
            'Timezone' => date_default_timezone_get(),
        ];

        return view('pages.dashboard.web.info', compact('data', 'phpInfo'));
    }
}
