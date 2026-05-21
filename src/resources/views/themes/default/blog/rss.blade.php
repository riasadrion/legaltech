<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }} Blog</title>
        <link>{{ url('/blog') }}</link>
        <description>Latest posts from {{ config('app.name') }}</description>
        <language>en</language>
        <atom:link href="{{ route('blog.rss') }}" rel="self" type="application/rss+xml" />
        @foreach($posts as $post)
        <item>
            <title>{{ htmlspecialchars($post->getLocalTitle()) }}</title>
            <link>{{ url(route('blog.show', $post->slug, false)) }}</link>
            <guid isPermaLink="true">{{ url(route('blog.show', $post->slug, false)) }}</guid>
            <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
            @if($post->getLocalExcerpt())
            <description>{{ htmlspecialchars($post->getLocalExcerpt()) }}</description>
            @endif
        </item>
        @endforeach
    </channel>
</rss>
