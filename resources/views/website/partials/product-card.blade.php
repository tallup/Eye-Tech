@php
    $product = $product ?? null;
    if (! $product) return;
    $imgPath = $product->image_url ?? $product->image ?? null;
    $img = $imgPath ? asset($imgPath) : asset('images/placeholder-product.png');
@endphp
<a href="#" class="group block rounded-xl border hairline bg-paper transition hover:border-ink/40">
    <div class="aspect-square overflow-hidden rounded-t-xl bg-paper-alt">
        <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy"
             class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
    </div>
    <div class="p-4">
        <div class="flex items-center justify-between gap-2">
            <h3 class="text-sm font-medium text-ink line-clamp-2">{{ $product->name }}</h3>
            @if(($product->stock_quantity ?? 0) > 0)
                <span class="pill-stock shrink-0">In stock</span>
            @endif
        </div>
        <p class="mt-2 font-display text-lg font-semibold tabular-nums">
            D {{ number_format($product->selling_price ?? $product->price ?? 0, 0) }}
        </p>
    </div>
</a>
