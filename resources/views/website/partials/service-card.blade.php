@php
    $service = $service ?? null;
    if (! $service) return;
    $variant = $variant ?? 'default';
@endphp
<article class="flex flex-col gap-4 rounded-2xl border hairline bg-paper p-6 transition hover:border-ink/40">
    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-brand-red/10 text-brand-red">
        @switch(Str::slug($service->name))
            @case('phone-unlocking-service')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                @break
            @case('app-installation-configuration')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg>
                @break
            @case('cloud-setup-sync')
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19a4.5 4.5 0 0 0 0-9 7 7 0 0 0-13.5 2.5A4.5 4.5 0 0 0 6.5 19z"/></svg>
                @break
            @default
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
        @endswitch
    </div>
    <h3 class="font-display text-xl font-semibold">{{ $service->name }}</h3>
    <p class="text-sm text-ink-soft leading-relaxed">{{ \Illuminate\Support\Str::limit($service->description ?? '', 140) }}</p>
    <div class="mt-auto flex items-end justify-between pt-2">
        <p class="font-display text-2xl font-semibold tabular-nums">D {{ number_format($service->price ?? 0, 0) }}</p>
        @if(! empty($service->estimated_duration))
            <span class="text-xs text-ink-soft">{{ $service->estimated_duration }} min</span>
        @endif
    </div>
    <a href="{{ \App\Support\Whatsapp::url("Hi EyeTech, I'd like to book the {$service->name} service.") }}" target="_blank" rel="noopener" class="btn-outline mt-2 justify-center text-sm">Book via WhatsApp</a>
</article>
