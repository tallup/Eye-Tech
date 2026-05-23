@extends('layouts.app')

@section('title', 'Services — EyeTech')
@section('meta_description', 'Phone unlocking, password reset, app installation, cloud setup, software fixes and screen repair. Same-day service at EyeTech.')

@section('content')

<section class="section pt-14 md:pt-20 pb-10">
    <div class="max-w-2xl">
        <p class="text-sm uppercase tracking-widest text-brand-red font-medium">Services</p>
        <h1 class="mt-3 font-display text-display-md md:text-display-lg font-semibold tracking-tight">Fixed today, not next week.</h1>
        <p class="mt-5 text-lg text-ink-soft">From unlocking to full repairs — clear quotes, no surprises.</p>
    </div>
</section>

<section class="section section-y">
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
