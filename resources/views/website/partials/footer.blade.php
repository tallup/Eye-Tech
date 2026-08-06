<footer class="mt-24 border-t hairline bg-paper-alt">
    <div class="section py-14 grid gap-10 md:grid-cols-4">
        <div>
            <img src="{{ asset('images/logo.png') }}" alt="EyeTech" class="h-16 w-auto">

            <p class="mt-3 text-sm text-ink-soft max-w-xs">
                Mobile phones, accessories and trusted repair.
            </p>
        </div>

        <div>
            <h4 class="text-sm font-semibold mb-3">Shop</h4>
            <ul class="space-y-2 text-sm text-ink-soft">
                <li><a href="{{ route('home') }}#shop" class="hover:text-ink">Phones</a></li>
                <li><a href="{{ route('home') }}#shop" class="hover:text-ink">Audio</a></li>
                <li><a href="{{ route('home') }}#shop" class="hover:text-ink">Chargers</a></li>
                <li><a href="{{ route('home') }}#shop" class="hover:text-ink">Cases</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-sm font-semibold mb-3">Services</h4>
            <ul class="space-y-2 text-sm text-ink-soft">
                <li><a href="{{ route('services') }}" class="hover:text-ink">Unlocking</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-ink">Password reset</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-ink">App install</a></li>
                <li><a href="{{ route('services') }}" class="hover:text-ink">Repair</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-sm font-semibold mb-3">Visit</h4>
            <ul class="space-y-2 text-sm text-ink-soft">
                {{-- CONTENT SLOT: replace with real address + hours. --}}
                <li>Serrekunda, The Gambia</li>
                <li>Open today 9am–8pm</li>
                <li><a href="{{ \App\Support\Whatsapp::url() }}" target="_blank" rel="noopener" class="hover:text-ink">WhatsApp us ▸</a></li>
            </ul>
        </div>
    </div>

    <div class="border-t hairline">
        <div class="section py-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-ink-soft">
            <p>&copy; {{ date('Y') }} EyeTech. All rights reserved.</p>
            <p>Made with care.</p>
        </div>
    </div>
</footer>
