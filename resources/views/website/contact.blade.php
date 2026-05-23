@extends('layouts.app')

@section('title', 'Contact — EyeTech')
@section('meta_description', 'Get in touch with EyeTech via WhatsApp, phone or by visiting our shop in Serrekunda.')

@section('content')

<section class="section pt-14 md:pt-20 pb-10">
    <div class="max-w-2xl">
        <p class="text-sm uppercase tracking-widest text-brand-red font-medium">Contact</p>
        <h1 class="mt-3 font-display text-display-md md:text-display-lg font-semibold tracking-tight">Get in touch.</h1>
        <p class="mt-5 text-lg text-ink-soft">Fastest reply is on WhatsApp. We're usually quick.</p>
    </div>
</section>

<section class="section section-y grid gap-8 md:grid-cols-2">
    <div class="space-y-4">
        @php
            // CONTENT SLOTS: replace placeholder values.
            $methods = [
                ['title' => 'WhatsApp',  'value' => '+220 000 0000',           'href' => '#'],
                ['title' => 'Phone',     'value' => '+220 000 0000',           'href' => 'tel:+2200000000'],
                ['title' => 'Email',     'value' => 'hello@eyetech.example',   'href' => 'mailto:hello@eyetech.example'],
                ['title' => 'Address',   'value' => 'Serrekunda, The Gambia',  'href' => '#'],
            ];
        @endphp
        @foreach ($methods as $m)
            <a href="{{ $m['href'] }}" class="block rounded-xl border hairline p-5 transition hover:border-ink/40">
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
