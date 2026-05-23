@extends('layouts.app')

@section('title', 'About — EyeTech')
@section('meta_description', 'EyeTech is a mobile phone shop offering phones, accessories and same-day repair in Serrekunda.')

@section('content')

<section class="section pt-10 md:pt-14 pb-8">
    <div class="grid items-center gap-8 md:gap-12 md:grid-cols-12">
        <div class="md:col-span-7">
            <p class="text-sm uppercase tracking-widest text-brand-red font-medium">About</p>
            <h1 class="mt-3 font-display text-display-md md:text-display-lg font-semibold tracking-tight">A real shop. Real people.</h1>
            <p class="mt-4 text-lg text-ink-soft max-w-xl">EyeTech opened to give the neighbourhood somewhere honest to buy a phone and somewhere reliable to fix one.</p>
        </div>
        <figure class="md:col-span-5 aspect-[4/3] overflow-hidden rounded-2xl bg-paper-alt">
            <img src="https://images.pexels.com/photos/1092671/pexels-photo-1092671.jpeg?auto=compress&w=900"
                 alt="Phone shop counter" loading="eager"
                 class="h-full w-full object-cover">
        </figure>
    </div>
</section>

<section class="section py-10 grid gap-10 md:grid-cols-2 md:items-center">
    <div class="reveal space-y-5 text-ink-soft leading-relaxed">
        {{-- CONTENT SLOT: replace with the owner's real story. --}}
        <p>We sell mainstream and budget phones, every accessory you actually need, and we handle the software side too — unlocking, password resets, app installs, cloud setup.</p>
        <p>If we don't have it, we'll tell you who does. If we can't fix it, we won't pretend we can.</p>
    </div>
    <div class="reveal">
        @include('website.partials.image-slot', [
            'src' => 'https://images.pexels.com/photos/1092671/pexels-photo-1092671.jpeg?auto=compress&w=1200',
            'alt' => 'Phone shop counter',
            'ratio' => 'aspect-[4/5]',
        ])
    </div>
</section>

<section class="bg-paper-alt">
    <div class="section section-y">
        <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight">What we sell</h2>
        <div class="mt-8 grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach (['Phones', 'Audio', 'Chargers', 'Cases'] as $cat)
                <div class="rounded-xl border hairline bg-paper p-6 text-center">
                    <p class="font-display text-lg font-semibold">{{ $cat }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section section-y border-t hairline">
    <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight">Visit us</h2>
    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <div class="rounded-2xl border hairline p-6">
            {{-- CONTENT SLOT: replace with real address + hours. --}}
            <p class="font-medium">Address</p>
            <p class="text-ink-soft mt-1">Serrekunda, The Gambia</p>
            <p class="mt-4 font-medium">Hours</p>
            <p class="text-ink-soft mt-1">Mon–Sat: 9am–8pm<br>Sunday: closed</p>
        </div>
        <div class="aspect-[4/3] rounded-2xl border hairline bg-paper-alt flex items-center justify-center text-ink-soft">
            {{-- MAP PLACEHOLDER: embed Google Map iframe here in v2. --}}
            Map coming soon
        </div>
    </div>
</section>

@include('website.partials.cta-band')

@endsection
