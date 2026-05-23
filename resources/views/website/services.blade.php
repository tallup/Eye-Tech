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
        <figure class="md:col-span-5 aspect-[4/3] overflow-hidden rounded-2xl bg-brand-red text-white flex flex-col justify-between p-6">
            <p class="text-xs uppercase tracking-widest opacity-80">Same-day</p>
            <p class="font-display text-3xl leading-tight">Most repairs done in hours, not days.</p>
            <p class="text-sm opacity-80">Walk in or message us on WhatsApp.</p>
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

{{-- OUR PROMISE --}}
<section class="bg-paper-alt">
    <div class="section section-y">
        <div class="grid gap-10 md:grid-cols-2 md:items-center">
            <div class="reveal">
                <p class="text-sm uppercase tracking-widest text-brand-red font-medium">Our promise</p>
                <h2 class="mt-3 font-display text-2xl md:text-3xl font-semibold tracking-tight">If we touch it, it's covered.</h2>
                <p class="mt-4 text-ink-soft leading-relaxed">Every screen and battery repair comes with a 30-day workmanship warranty. If anything we did fails in that window, we make it right — no debate, no re-charge.</p>
                <ul class="mt-6 space-y-2 text-ink-soft">
                    <li class="flex items-start gap-2"><span class="text-brand-red">✓</span> Up-front quote before we open the device</li>
                    <li class="flex items-start gap-2"><span class="text-brand-red">✓</span> Genuine or grade-A parts</li>
                    <li class="flex items-start gap-2"><span class="text-brand-red">✓</span> 30-day workmanship warranty</li>
                    <li class="flex items-start gap-2"><span class="text-brand-red">✓</span> Same-day completion for most jobs</li>
                </ul>
            </div>
            <figure class="reveal aspect-[4/5] overflow-hidden rounded-2xl bg-paper">
                <img src="https://images.pexels.com/photos/4350099/pexels-photo-4350099.jpeg?auto=compress&w=900"
                     alt="Technician carefully working on a phone" loading="lazy"
                     class="h-full w-full object-cover">
            </figure>
        </div>
    </div>
</section>

@include('website.partials.cta-band', [
    'heading' => "Don't see what you need?",
    'button_text' => 'Ask on WhatsApp',
    'button_href' => \App\Support\Whatsapp::url('Hi EyeTech, I need help with something specific.'),
])

@endsection
