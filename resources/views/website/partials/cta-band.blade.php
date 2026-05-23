@php
    $heading = $heading ?? 'Need help with your phone today?';
    $button_text = $button_text ?? 'Chat on WhatsApp';
    $button_href = $button_href ?? '#';
@endphp
<section class="bg-brand-red text-white">
    <div class="section section-y flex flex-col items-center text-center gap-6">
        <h2 class="font-display text-3xl md:text-4xl font-semibold tracking-tight">{{ $heading }}</h2>
        <a href="{{ $button_href }}" class="inline-flex items-center gap-2 rounded-full bg-white px-7 py-3 font-medium text-brand-red transition hover:bg-paper">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
            {{ $button_text }}
        </a>
    </div>
</section>
