{!! '<'.'?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ $landing }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>
    @foreach ($items as $item)
    <url>
        <loc>{{ url('/'.$item->slug) }}</loc>
        <lastmod>{{ \Illuminate\Support\Carbon::parse($item->updated_at)->toAtomString() }}</lastmod>
        <changefreq>weekly</changefreq>
        <priority>0.8</priority>
    </url>
    @endforeach
</urlset>
