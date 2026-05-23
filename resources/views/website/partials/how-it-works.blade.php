<section class="section section-y">
    <h2 class="font-display text-2xl md:text-3xl font-semibold tracking-tight">How it works</h2>
    <div class="mt-10 grid gap-8 md:grid-cols-3">
        @foreach ([
            ['01', 'Walk in or message us', 'Stop by the shop or send a quick WhatsApp.'],
            ['02', 'We diagnose & quote',   'Clear price, no surprises, before we touch anything.'],
            ['03', 'Done the same day',     'Most jobs finished within hours, not days.'],
        ] as $step)
            <div class="reveal">
                <div class="font-display text-5xl font-semibold text-brand-red tabular-nums">{{ $step[0] }}</div>
                <h3 class="mt-4 font-display text-xl font-semibold">{{ $step[1] }}</h3>
                <p class="mt-2 text-sm text-ink-soft leading-relaxed">{{ $step[2] }}</p>
            </div>
        @endforeach
    </div>
</section>
