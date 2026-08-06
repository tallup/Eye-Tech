@php
    $service = $service ?? null;
    if (! $service) return;
    $slug = \Illuminate\Support\Str::slug($service->name);

    // Each entry is either:
    //   ['img',  '<pexels-url>', '<alt>']        — subject-verified, safe
    //   ['tile', '<eyebrow>', '<headline>']     — brand-red text tile (no image risk)
    $map = [
        'phone-unlocking-service'        => ['img',  'https://images.pexels.com/photos/4350099/pexels-photo-4350099.jpeg?auto=compress&w=800', 'Technician unlocking a phone'],
        'app-installation-configuration' => ['img',  'https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&w=800', 'Phone home screen full of apps'],
        'cloud-setup-sync'               => ['tile', 'Backup',       'Cloud backup &amp; sync, configured for you.'],
        'software-installation-updates'  => ['tile', 'Software',     'OS &amp; updates, installed right.'],
        'virus-malware-removal'          => ['tile', 'Security',     'Clean phone, fast.'],
        'data-recovery-backup'           => ['tile', 'Recovery',     'Photos back, even from a broken phone.'],
        'performance-optimization'       => ['img',  'https://images.pexels.com/photos/1092644/pexels-photo-1092644.jpeg?auto=compress&w=800', 'Smooth-running smartphone in hand'],
        'network-connectivity-setup'     => ['img',  'https://images.pexels.com/photos/4526481/pexels-photo-4526481.jpeg?auto=compress&w=800', 'Cable plugged into a phone'],
    ];

    $entry = $map[$slug] ?? ['tile', 'EyeTech service', $service->name];
@endphp
@if ($entry[0] === 'img')
<figure class="relative aspect-[16/10] overflow-hidden rounded-xl bg-paper-alt">
    <img src="{{ $entry[1] }}" alt="{{ $entry[2] }}" loading="lazy"
         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
</figure>
@else
<figure class="relative aspect-[16/10] overflow-hidden rounded-xl bg-brand-red text-white flex flex-col justify-between p-5">
    <p class="text-xs uppercase tracking-widest opacity-80">{{ $entry[1] }}</p>
    <p class="font-display text-xl leading-tight">{!! $entry[2] !!}</p>
</figure>
@endif
