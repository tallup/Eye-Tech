{{--
    IMAGE SLOT
    Replace `src` with an owner-supplied photo once available.
    Required vars: $src, $alt
    Optional vars: $ratio (default 'aspect-[4/3]'), $rounded (default 'rounded-xl'), $loading (default 'lazy')
--}}
@php
    $ratio   = $ratio   ?? 'aspect-[4/3]';
    $rounded = $rounded ?? 'rounded-xl';
    $loading = $loading ?? 'lazy';
@endphp
<figure class="relative overflow-hidden {{ $ratio }} {{ $rounded }} bg-paper-alt">
    <img
        src="{{ $src }}"
        alt="{{ $alt }}"
        loading="{{ $loading }}"
        class="absolute inset-0 h-full w-full object-cover transition duration-500 hover:scale-[1.02]"
    >
</figure>
