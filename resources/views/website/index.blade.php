@extends('layouts.app')

@section('title', 'EyeTech — Phones, accessories & trusted repair')
@section('meta_description', 'EyeTech sells phones and accessories and offers same-day repair, unlocking and software services in Serrekunda.')

@section('content')

{{-- HERO --}}
<section class="section pt-14 md:pt-20 pb-16 md:pb-24">
    <div class="grid items-center gap-10 md:gap-16 md:grid-cols-2">
        <div class="reveal">
            <h1 class="font-display text-display-lg md:text-display-xl font-semibold tracking-tight">
                Mobile phones,<br>accessories &<br><span class="text-brand-red">trusted repair.</span>
            </h1>
            <p class="mt-6 text-lg text-ink-soft max-w-md">
                Visit our shop or message us — we'll sort you out the same day.
            </p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#shop" class="btn-primary">Visit shop</a>
                <a href="#" class="btn-outline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    WhatsApp us
                </a>
            </div>
            <div class="mt-8 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-ink-soft">
                <span class="inline-flex items-center gap-1.5">
                    <span class="text-brand-red">★★★★★</span> 4.9
                </span>
                <span class="pill-open">Open today 9am–8pm</span>
                <a href="{{ route('contact') }}" class="hover:text-ink">Map ▸</a>
            </div>
        </div>
        <div class="reveal">
            @include('website.partials.image-slot', [
                'src'     => 'https://images.pexels.com/photos/1092644/pexels-photo-1092644.jpeg?auto=compress&w=1200',
                'alt'     => 'Hands holding a smartphone',
                'ratio'   => 'aspect-[4/5]',
                'loading' => 'eager',
            ])
        </div>
    </div>
</section>

{{-- POPULAR PRODUCTS --}}
<section id="shop" class="section section-y border-t hairline">
    <div class="flex items-end justify-between gap-4">
        <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight">Popular right now</h2>
        <a href="{{ route('services') }}" class="text-sm font-medium hover:text-brand-red">View all ▸</a>
    </div>
    @if($featuredProducts->isEmpty())
        <p class="mt-8 text-ink-soft">No products available right now.</p>
    @else
        <div class="mt-8 grid gap-5 grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($featuredProducts as $product)
                <div class="reveal">
                    @include('website.partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- SERVICES BAND --}}
<section class="bg-paper-alt">
    <div class="section section-y">
        <div class="flex items-end justify-between gap-4">
            <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight">Same-day service</h2>
            <a href="{{ route('services') }}" class="text-sm font-medium hover:text-brand-red">All services ▸</a>
        </div>
        @if($services->isEmpty())
            <p class="mt-8 text-ink-soft">Services coming soon.</p>
        @else
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach($services as $service)
                    <div class="reveal">
                        @include('website.partials.service-card', ['service' => $service])
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@include('website.partials.how-it-works')

{{-- TRUST BAND --}}
<section class="section section-y border-t hairline">
    <div class="grid gap-10 md:grid-cols-3 md:items-center">
        <blockquote class="md:col-span-2 font-display text-2xl md:text-3xl leading-snug text-ink">
            "Took my phone in, walked out an hour later with everything working.<br>Fair price, no drama."
            <footer class="mt-4 text-sm text-ink-soft">— A real customer (replace with actual quote)</footer>
        </blockquote>
        <ul class="grid grid-cols-3 md:grid-cols-1 gap-4 text-sm">
            <li class="rounded-xl border hairline p-4">
                <p class="font-display text-2xl font-semibold tabular-nums">5+</p>
                <p class="text-ink-soft">years serving the area</p>
            </li>
            <li class="rounded-xl border hairline p-4">
                <p class="font-display text-2xl font-semibold tabular-nums">2,000+</p>
                <p class="text-ink-soft">repairs done</p>
            </li>
            <li class="rounded-xl border hairline p-4">
                <p class="font-display text-2xl font-semibold tabular-nums">100%</p>
                <p class="text-ink-soft">genuine accessories</p>
            </li>
        </ul>
    </div>
</section>

@include('website.partials.cta-band')

@endsection
