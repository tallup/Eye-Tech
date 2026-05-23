@extends('layouts.app')

@section('title', 'Services — EyeTech')
@section('meta_description', 'Phone unlocking, password reset, app installation, cloud setup, software fixes and screen repair. Same-day service at EyeTech.')

@section('content')

<section class="section pt-10 md:pt-14 pb-10">
    <div class="grid items-center gap-8 md:gap-12 md:grid-cols-12">
        <div class="md:col-span-7">
            <p class="text-sm uppercase tracking-widest text-brand-red font-medium">Services</p>
            <h1 class="mt-3 font-display text-display-md md:text-display-lg font-semibold tracking-tight">Fixed today, not next week.</h1>
            <p class="mt-4 text-lg text-ink-soft max-w-xl">From unlocking to full repairs — clear quotes, no surprises.</p>
        </div>
        <figure class="md:col-span-5 aspect-[4/3] overflow-hidden rounded-2xl bg-paper-alt">
            <img src="https://images.pexels.com/photos/4350099/pexels-photo-4350099.jpeg?auto=compress&w=900"
                 alt="Technician repairing a phone" loading="eager"
                 class="h-full w-full object-cover">
        </figure>
    </div>
</section>

<section class="section pt-2 pb-12">
    @if($services->isEmpty())
        <p class="text-ink-soft">No services listed right now.</p>
    @else
        <div class="grid gap-6 md:grid-cols-2">
            @foreach($services as $service)
                <div class="reveal">
                    @include('website.partials.service-card', ['service' => $service])
                </div>
            @endforeach
        </div>
    @endif
</section>

@include('website.partials.how-it-works')

@include('website.partials.cta-band', [
    'heading' => "Don't see what you need?",
    'button_text' => 'Ask on WhatsApp',
])

@endsection
