@extends('layouts.app')

@section('title', 'Contact — EyeTech')
@section('meta_description', 'Get in touch with EyeTech via WhatsApp, phone or by visiting our shop in Serrekunda.')

@section('content')

<section class="section pt-10 md:pt-14 pb-8">
    <div class="grid items-end gap-8 md:gap-12 md:grid-cols-12">
        <div class="md:col-span-7">
            <p class="text-sm uppercase tracking-widest text-brand-red font-medium">Contact</p>
            <h1 class="mt-3 font-display text-display-md md:text-display-lg font-semibold tracking-tight">Get in touch.</h1>
            <p class="mt-4 text-lg text-ink-soft max-w-xl">Fastest reply is on WhatsApp. We're usually quick.</p>
        </div>
        <figure class="md:col-span-5 aspect-[4/3] overflow-hidden rounded-2xl bg-paper-alt">
            <img src="https://images.pexels.com/photos/4068314/pexels-photo-4068314.jpeg?auto=compress&w=900"
                 alt="Shop counter" loading="eager"
                 class="h-full w-full object-cover">
        </figure>
    </div>
</section>

<section class="section section-y grid gap-8 md:grid-cols-2">
    <div class="space-y-4">
        @php
            // CONTENT SLOTS: replace placeholder values.
            $methods = [
                ['title' => 'WhatsApp',  'value' => config('site.phone_display'),  'href' => \App\Support\Whatsapp::url(),                                              'external' => true],
                ['title' => 'Phone',     'value' => config('site.phone_display'),  'href' => 'tel:' . preg_replace('/\D+/', '', config('site.phone_display')),           'external' => false],
                ['title' => 'Email',     'value' => 'hello@eyetech.example',       'href' => 'mailto:hello@eyetech.example',                                            'external' => false],
                ['title' => 'Address',   'value' => 'Serrekunda, The Gambia',      'href' => '#',                                                                       'external' => false],
            ];
        @endphp
        @foreach ($methods as $m)
            <a href="{{ $m['href'] }}" @if(!empty($m['external'])) target="_blank" rel="noopener" @endif class="block rounded-xl border hairline p-5 transition hover:border-ink/40">
                <p class="text-sm text-ink-soft">{{ $m['title'] }}</p>
                <p class="mt-1 font-display text-xl font-semibold">{{ $m['value'] }}</p>
            </a>
        @endforeach
    </div>

    <div class="rounded-2xl border hairline p-6">
        @if (session('status'))
            <p class="mb-4 rounded-md bg-success-brand/10 text-success-brand p-3 text-sm">{{ session('status') }}</p>
        @endif
        <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium">Name</label>
                <input id="name" name="name" type="text" required value="{{ old('name') }}"
                       class="mt-1 w-full rounded-md border-line focus:border-brand-red focus:ring-brand-red">
                @error('name') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium">Phone or WhatsApp</label>
                <input id="phone" name="phone" type="text" required value="{{ old('phone') }}"
                       class="mt-1 w-full rounded-md border-line focus:border-brand-red focus:ring-brand-red">
                @error('phone') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-medium">How can we help?</label>
                <textarea id="message" name="message" rows="5" required
                          class="mt-1 w-full rounded-md border-line focus:border-brand-red focus:ring-brand-red">{{ old('message') }}</textarea>
                @error('message') <p class="mt-1 text-sm text-brand-red">{{ $message }}</p> @enderror
            </div>
            <button type="submit" class="btn-primary w-full justify-center">Send</button>
        </form>
    </div>
</section>

@include('website.partials.cta-band')

@endsection
