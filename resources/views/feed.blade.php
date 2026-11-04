{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/">
    <channel>
        <title>{{ config('app.name', 'Bingkai') }}</title>
        <link>{{ route('images.index') }}</link>
        <description>Latest photographs from {{ config('app.name', 'Bingkai') }}.</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <lastBuildDate>{{ ($images->first()?->updated_at ?? now())->format('D, d M Y H:i:s O') }}</lastBuildDate>
        <atom:link href="{{ route('feed') }}" rel="self" type="application/rss+xml" />
        @foreach ($images as $image)
            <item>
                <title>{{ $image->display_title }}</title>
                <link>{{ route('images.show', $image->slug) }}</link>
                <guid isPermaLink="true">{{ route('images.show', $image->slug) }}</guid>
                <pubDate>{{ ($image->taken_at ?? $image->created_at)->format('D, d M Y H:i:s O') }}</pubDate>
                <description>{{ $image->description ?: $image->display_title }}</description>
                <media:content url="{{ url($image->standard_url) }}" medium="image" type="{{ $image->mime_type }}" />
            </item>
        @endforeach
    </channel>
</rss>
