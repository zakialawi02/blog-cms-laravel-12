<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Newsletter</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545E;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .header {
            background-color: #2d3748;
            padding: 30px 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 40px 30px;
        }

        .intro {
            margin-bottom: 30px;
            text-align: center;
        }

        .intro h2 {
            margin-top: 0;
            color: #333333;
            font-size: 20px;
        }

        .intro p {
            font-size: 16px;
            color: #6b7280;
        }

        .article-card {
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .article-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
            border-bottom: 1px solid #e5e7eb;
        }

        .article-body {
            padding: 20px;
        }

        .article-title {
            margin: 0 0 10px;
            font-size: 18px;
            font-weight: 600;
        }

        .article-title a {
            color: #2d3748;
            text-decoration: none;
        }

        .article-excerpt {
            font-size: 14px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .read-more-btn {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .read-more-btn:hover {
            background-color: #2563eb;
        }

        .footer {
            background-color: #f4f4f7;
            padding: 30px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
        }

        .footer a {
            color: #6b7280;
            text-decoration: underline;
        }

        .footer p {
            margin: 5px 0;
        }

        /* Mobile adjustment */
        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
                border-radius: 0 !important;
            }

            .content {
                padding: 20px !important;
            }
        }
    </style>
    </meta>

<body>
    @php
        $siteName = \App\Models\WebSetting::getSetting('site_name', 'Our Blog');
        $siteUrl = config('app.url');
        $currentYear = date('Y');
    @endphp

    <div style="padding: 20px 0;">
        <div class="container">
            <!-- Header -->
            <div class="header">
                <h1>{{ $siteName }} Weekly</h1>
            </div>

            <!-- Content -->
            <div class="content">
                <div class="intro">
                    <h2>Latest Updates</h2>
                    <p>Here are the top stories from this week just for you.</p>
                </div>

                @foreach ($articles as $article)
                    <div class="article-card">
                        @if ($article->cover)
                            <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                <img src="{{ asset($article->cover) }}" alt="{{ $article->title }}" style="width: 100%; height: 200px; object-fit: cover;">
                            </a>
                        @endif
                        <div class="article-body">
                            <h3 class="article-title">
                                <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">
                                    {{ $article->title }}
                                </a>
                            </h3>
                            <p class="article-excerpt">{{ Str::limit($article->excerpt, 120) }}</p>
                            <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}" class="read-more-btn" style="color: #ffffff;">
                                Read Article
                            </a>
                        </div>
                    </div>
                @endforeach

                @if (isset($randomPosts) && count($randomPosts) > 0)
                    <div style="border-top: 1px solid #e5e7eb; margin: 40px 0 30px; position: relative;">
                        <span style="background: #fff; padding: 0 15px; color: #6b7280; position: absolute; top: -12px; left: 50%; transform: translateX(-50%); font-size: 14px; font-weight: 500;">You might also like</span>
                    </div>

                    @foreach ($randomPosts as $post)
                        <div style="margin-bottom: 20px; display: flex; align-items: start;">
                            @if ($post->cover)
                                <div style="flex-shrink: 0; width: 80px; height: 80px; margin-right: 15px;">
                                    <a href="{{ route('article.show', ['year' => $post->published_at->format('Y'), 'slug' => $post->slug]) }}">
                                        <img src="{{ asset($post->cover) }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 4px;">
                                    </a>
                                </div>
                            @endif
                            <div>
                                <h4 style="margin: 0 0 5px; font-size: 16px;">
                                    <a href="{{ route('article.show', ['year' => $post->published_at->format('Y'), 'slug' => $post->slug]) }}" style="color: #2d3748; text-decoration: none;">
                                        {{ $post->title }}
                                    </a>
                                </h4>
                                <a href="{{ route('article.show', ['year' => $post->published_at->format('Y'), 'slug' => $post->slug]) }}" style="font-size: 13px; color: #3b82f6; text-decoration: none;">Read more</a>
                            </div>
                        </div>
                    @endforeach
                @endif

            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; {{ $currentYear }} {{ $siteName }}. All rights reserved.</p>
                <p>
                    You are receiving this email because you subscribed to our newsletter.<br>
                    <a href="{{ $siteUrl }}">Visit our website</a>
                </p>
                <p style="margin-top: 15px;">
                    <a href="{{ URL::signedRoute('newsletter.unsubscribe', ['newsletter' => $subscriber->id]) }}">
                        Unsubscribe from this list
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>

</html>
