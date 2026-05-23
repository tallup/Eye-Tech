<header data-topnav class="sticky top-0 z-40 bg-paper/90 backdrop-blur supports-[backdrop-filter]:bg-paper/75 border-b hairline">
    <div class="section flex h-18 items-center justify-between py-3">
        <a href="{{ route('home') }}" class="flex items-center" aria-label="EyeTech home">
            <img src="{{ asset('images/logo.png') }}" alt="EyeTech — Makes Your Day Easy" class="h-12 w-auto">
        </a>

        <nav class="hidden md:flex items-center gap-8 text-[15px] font-medium">
            <a href="{{ route('home') }}#shop"     class="text-ink-soft hover:text-ink transition">Shop</a>
            <a href="{{ route('services') }}"     class="text-ink-soft hover:text-ink transition">Services</a>
            <a href="{{ route('about') }}"        class="text-ink-soft hover:text-ink transition">About</a>
            <a href="{{ route('contact') }}"      class="text-ink-soft hover:text-ink transition">Contact</a>
        </nav>

        <div class="hidden md:flex items-center">
            <a href="{{ \App\Support\Whatsapp::url() }}" target="_blank" rel="noopener" class="btn-primary text-sm" aria-label="Chat with us on WhatsApp">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                Chat with us
            </a>
        </div>

        <button data-nav-toggle class="md:hidden inline-flex items-center justify-center rounded-md p-2 text-ink hover:bg-paper-alt focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-red"
                aria-expanded="false" aria-controls="mobile-nav">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            <span class="sr-only">Open menu</span>
        </button>
    </div>

    <div id="mobile-nav" data-nav-panel class="hidden md:hidden border-t hairline">
        <div class="section py-4 grid gap-3 text-base font-medium">
            <a href="{{ route('home') }}#shop" class="py-2">Shop</a>
            <a href="{{ route('services') }}"  class="py-2">Services</a>
            <a href="{{ route('about') }}"     class="py-2">About</a>
            <a href="{{ route('contact') }}"   class="py-2">Contact</a>
            <a href="{{ \App\Support\Whatsapp::url() }}" target="_blank" rel="noopener" class="btn-primary mt-2 justify-center">Chat on WhatsApp</a>
        </div>
    </div>
</header>
