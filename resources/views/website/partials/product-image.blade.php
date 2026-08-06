@php
    $product = $product ?? null;
    if (! $product) return;
    $alt = $product->name;
    $placeholder = asset('images/placeholder-product.png');

    // Try the real image on disk first.
    $imgPath = $product->image_url ?? $product->image ?? null;
    $diskCandidate = $imgPath
        ? public_path('images/' . ltrim($imgPath, '/'))
        : null;
    $useReal = $diskCandidate && is_file($diskCandidate);

    if ($useReal) {
        $src = asset('images/' . ltrim($imgPath, '/'));
    } else {
        // Deterministic per-category Pexels fallback.
        // Slugs are derived from actual DB category names via Str::slug().
        // All URLs verified image/jpeg 200 on 2026-05-23.
        // These IDs are distinct from all page-level slot images.
        $byCategory = [
            'screen-protectors'  => 'https://images.pexels.com/photos/4790286/pexels-photo-4790286.jpeg?auto=compress&w=800',
            'phone-cases'        => 'https://images.pexels.com/photos/4790286/pexels-photo-4790286.jpeg?auto=compress&w=800',
            'chargers-cables'    => 'https://images.pexels.com/photos/4790287/pexels-photo-4790287.jpeg?auto=compress&w=800',
            'audio-headphones'   => 'https://images.pexels.com/photos/4790288/pexels-photo-4790288.jpeg?auto=compress&w=800',
            'gaming-accessories' => 'https://images.pexels.com/photos/5081932/pexels-photo-5081932.jpeg?auto=compress&w=800',
            'accessories'        => 'https://images.pexels.com/photos/5081932/pexels-photo-5081932.jpeg?auto=compress&w=800',
            // legacy / alias slugs kept for safety
            'phones'             => 'https://images.pexels.com/photos/5081933/pexels-photo-5081933.jpeg?auto=compress&w=800',
            'mobile-phones'      => 'https://images.pexels.com/photos/5081933/pexels-photo-5081933.jpeg?auto=compress&w=800',
            'smartphones'        => 'https://images.pexels.com/photos/5081933/pexels-photo-5081933.jpeg?auto=compress&w=800',
            'headphones'         => 'https://images.pexels.com/photos/4790288/pexels-photo-4790288.jpeg?auto=compress&w=800',
            'earbuds'            => 'https://images.pexels.com/photos/4790288/pexels-photo-4790288.jpeg?auto=compress&w=800',
            'chargers'           => 'https://images.pexels.com/photos/4790287/pexels-photo-4790287.jpeg?auto=compress&w=800',
            'cables'             => 'https://images.pexels.com/photos/4790287/pexels-photo-4790287.jpeg?auto=compress&w=800',
            'cases'              => 'https://images.pexels.com/photos/4790286/pexels-photo-4790286.jpeg?auto=compress&w=800',
        ];
        $catSlug = \Illuminate\Support\Str::slug($product->category?->name ?? '');
        $src = $byCategory[$catSlug]
            ?? 'https://images.pexels.com/photos/5081932/pexels-photo-5081932.jpeg?auto=compress&w=800';
    }
@endphp
<img src="{{ $src }}" alt="{{ $alt }}" loading="lazy"
     onerror="this.onerror=null;this.src='{{ $placeholder }}'"
     class="{{ $class ?? 'h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]' }}">
