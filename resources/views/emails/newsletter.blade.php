<!DOCTYPE html>
<html>

<head>
    <title>Weekly Newsletter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            background: #007bff;
            color: #ffffff;
            padding: 10px 0;
            border-radius: 8px 8px 0 0;
        }

        .article {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }

        .article:last-child {
            border-bottom: none;
        }

        .article h2 {
            margin: 0 0 10px;
            font-size: 18px;
        }

        .article h2 a {
            color: #333;
            text-decoration: none;
        }

        .article p {
            font-size: 14px;
            color: #555;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-top: 20px;
        }
    </style>
</head>

<body>

    <div class="container">
        <div class="header">
            <h1>Weekly Newsletter</h1>
            <p>Here are the latest updates from our blog!</p>
        </div>

        @foreach ($articles as $article)
            <div class="article">
                @if ($article->cover)
                    <img src="{{ asset($article->cover) }}" alt="{{ $article->title }}" style="width: 100%; max-width: 600px; height: auto; border-radius: 5px; margin-bottom: 10px;">
                @endif
                <h2>
                    <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}">{{ $article->title }}</a>
                </h2>
                <p>{{ Str::limit($article->excerpt, 100) }}</p>
                <a href="{{ route('article.show', ['year' => $article->published_at->format('Y'), 'slug' => $article->slug]) }}" style="color: #007bff; text-decoration: none; font-weight: bold;">Read More &rarr;</a>
            </div>
        @endforeach

        <div class="footer">
            <p>Thank you for subscribing to our newsletter.</p>
            <p>
                <a href="{{ URL::signedRoute('newsletter.unsubscribe', ['newsletter' => $subscriber->id]) }}" style="color: #888; text-decoration: underline;">Unsubscribe</a>
            </p>
        </div>
    </div>

</body>

</html>
