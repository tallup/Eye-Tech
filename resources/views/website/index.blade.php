@extends('layouts.app')

@section('title', 'EyeTech — Phones, accessories & trusted repair')
@section('meta_description', 'EyeTech sells phones and accessories and offers same-day repair, unlocking and software services in Serrekunda.')

@section('content')

{{-- HERO --}}
<section class="section pt-10 md:pt-14 pb-12 md:pb-16">
    <div class="grid items-center gap-10 md:gap-12 md:grid-cols-12">
        <div class="reveal md:col-span-6">
            <p class="text-sm uppercase tracking-widest text-brand-red font-medium">Mobile phones &amp; repair</p>
            <h1 class="mt-3 font-display text-display-lg md:text-display-xl font-semibold tracking-tight">
                Phones,<br>accessories &amp;<br><span class="text-brand-red">trusted repair.</span>
            </h1>
            <p class="mt-5 text-lg text-ink-soft max-w-md">
                Visit our shop or message us — we'll sort you out the same day.
            </p>
            <div class="mt-6 flex flex-wrap gap-3">
                <a href="#shop" class="btn-primary">Visit shop</a>
                <a href="{{ \App\Support\Whatsapp::url() }}" target="_blank" rel="noopener" class="btn-outline">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    WhatsApp us
                </a>
            </div>
            <div class="mt-6 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-ink-soft">
                <span class="inline-flex items-center gap-1.5"><span class="text-brand-red">★★★★★</span> 4.9</span>
                <span class="pill-open">Open today 9am–8pm</span>
                <a href="{{ route('contact') }}" class="hover:text-ink">Map ▸</a>
            </div>
        </div>
        <div class="reveal md:col-span-6 grid grid-cols-6 grid-rows-6 gap-3 h-[460px] md:h-[520px]">
            <figure class="col-span-4 row-span-6 overflow-hidden rounded-2xl bg-paper-alt">
                <img src="https://images.pexels.com/photos/1092644/pexels-photo-1092644.jpeg?auto=compress&w=1000"
                     alt="Customer holding a smartphone" loading="eager"
                     class="h-full w-full object-cover">
            </figure>
            <figure class="col-span-2 row-span-3 overflow-hidden rounded-2xl bg-paper-alt">
                <img src="https://images.pexels.com/photos/3945667/pexels-photo-3945667.jpeg?auto=compress&w=600"
                     alt="Phone accessories" loading="eager"
                     class="h-full w-full object-cover">
            </figure>
            <figure class="col-span-2 row-span-3 overflow-hidden rounded-2xl bg-brand-red text-white p-5 flex flex-col justify-between">
                <p class="text-xs uppercase tracking-widest opacity-80">Same-day</p>
                <p class="font-display text-2xl leading-tight">Repairs done in hours.</p>
            </figure>
        </div>
    </div>
</section>

{{-- CATEGORIES --}}
<section class="section pb-10 md:pb-12">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-4">
        @php
            $cats = [
                ['Phones',      'https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&w=600'],
                ['Audio',       'https://images.pexels.com/photos/3587478/pexels-photo-3587478.jpeg?auto=compress&w=600'],
                ['Chargers',    'https://images.pexels.com/photos/4526481/pexels-photo-4526481.jpeg?auto=compress&w=600'],
                ['Cases',       'https://images.pexels.com/photos/1647976/pexels-photo-1647976.jpeg?auto=compress&w=600'],
            ];
        @endphp
        @foreach ($cats as [$name, $src])
            <a href="#shop" class="group relative aspect-[4/3] overflow-hidden rounded-2xl bg-paper-alt">
                <img src="{{ $src }}" alt="{{ $name }}" loading="lazy"
                     class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/0 to-transparent"></div>
                <div class="absolute bottom-3 left-4 right-4 text-white font-display text-lg font-semibold">
                    {{ $name }}
                </div>
            </a>
        @endforeach
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

{{-- BRANDS STRIP --}}
<section class="section pt-8 md:pt-10 pb-2">
    <p class="text-xs uppercase tracking-widest text-ink-soft text-center">Brands we carry</p>
    <div class="mt-5 flex flex-wrap items-center justify-center gap-x-10 gap-y-5">
        @php
            $logoBrands = [
                ['name' => 'Apple',    'slug' => 'apple'],
                ['name' => 'Samsung',  'slug' => 'samsung'],
                ['name' => 'Xiaomi',   'slug' => 'xiaomi'],
                ['name' => 'Huawei',   'slug' => 'huawei'],
            ];
            $textBrands = ['Tecno', 'Infinix', 'Itel', 'Oraimo'];
        @endphp
        @foreach ($logoBrands as $b)
            <img src="https://cdn.simpleicons.org/{{ $b['slug'] }}/4B4B4B"
                 alt="{{ $b['name'] }}"
                 loading="lazy"
                 class="h-7 md:h-8 w-auto opacity-70 hover:opacity-100 transition">
        @endforeach
        @foreach ($textBrands as $name)
            <span class="font-display text-lg md:text-xl font-semibold text-ink-soft opacity-80">{{ $name }}</span>
        @endforeach
    </div>
</section>

@include('website.partials.how-it-works')

{{-- WORKSHOP VISUAL BAND --}}
<section class="section pb-10">
    <div class="grid gap-3 md:grid-cols-4">
        <figure class="overflow-hidden rounded-2xl aspect-[4/5] md:aspect-square bg-paper-alt md:col-span-2 md:row-span-2 md:aspect-auto">
            <img src="https://images.pexels.com/photos/4350099/pexels-photo-4350099.jpeg?auto=compress&w=1000"
                 alt="Technician repairing a phone" loading="lazy"
                 class="h-full w-full object-cover">
        </figure>
        <figure class="overflow-hidden rounded-2xl aspect-square bg-paper-alt">
            <img src="https://images.pexels.com/photos/4316/electronics-mobile-phone-screen.jpg?auto=compress&w=600"
                 alt="Disassembled phone parts" loading="lazy"
                 class="h-full w-full object-cover">
        </figure>
        <figure class="overflow-hidden rounded-2xl aspect-square bg-paper-alt">
            <img src="https://images.pexels.com/photos/4068366/pexels-photo-4068366.jpeg?auto=compress&w=600"
                 alt="Repair tools" loading="lazy"
                 class="h-full w-full object-cover">
        </figure>
        <figure class="overflow-hidden rounded-2xl aspect-square bg-paper-alt">
            <img src="https://images.pexels.com/photos/3945667/pexels-photo-3945667.jpeg?auto=compress&w=600"
                 alt="Accessories laid out" loading="lazy"
                 class="h-full w-full object-cover">
        </figure>
        <figure class="overflow-hidden rounded-2xl aspect-square bg-paper-alt">
            <img src="https://images.pexels.com/photos/4068314/pexels-photo-4068314.jpeg?auto=compress&w=600"
                 alt="Workshop bench" loading="lazy"
                 class="h-full w-full object-cover">
        </figure>
    </div>
</section>

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
