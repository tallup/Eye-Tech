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
        $byCategory = [
            'screen-protectors'  => 'https://images.pexels.com/photos/1647976/pexels-photo-1647976.jpeg?auto=compress&w=800',
            'phone-cases'        => 'https://images.pexels.com/photos/1647976/pexels-photo-1647976.jpeg?auto=compress&w=800',
            'chargers-cables'    => 'https://images.pexels.com/photos/4526481/pexels-photo-4526481.jpeg?auto=compress&w=800',
            'audio-headphones'   => 'https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&w=800',
            'gaming-accessories' => 'https://images.pexels.com/photos/3945667/pexels-photo-3945667.jpeg?auto=compress&w=800',
            'accessories'        => 'https://images.pexels.com/photos/3945667/pexels-photo-3945667.jpeg?auto=compress&w=800',
            // legacy / alias slugs kept for safety
            'phones'             => 'https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&w=800',
            'mobile-phones'      => 'https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&w=800',
            'smartphones'        => 'https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&w=800',
            'headphones'         => 'https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&w=800',
            'earbuds'            => 'https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&w=800',
            'chargers'           => 'https://images.pexels.com/photos/4526481/pexels-photo-4526481.jpeg?auto=compress&w=800',
            'cables'             => 'https://images.pexels.com/photos/4526481/pexels-photo-4526481.jpeg?auto=compress&w=800',
            'cases'              => 'https://images.pexels.com/photos/1647976/pexels-photo-1647976.jpeg?auto=compress&w=800',
        ];
        $catSlug = \Illuminate\Support\Str::slug($product->category?->name ?? '');
        $src = $byCategory[$catSlug]
            ?? 'https://images.pexels.com/photos/3945667/pexels-photo-3945667.jpeg?auto=compress&w=800';
    }
@endphp
<img src="{{ $src }}" alt="{{ $alt }}" loading="lazy"
     onerror="this.onerror=null;this.src='{{ $placeholder }}'"
     class="{{ $class ?? 'h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]' }}">
