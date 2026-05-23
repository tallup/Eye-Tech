@php
    $service = $service ?? null;
    if (! $service) return;
    $slug = \Illuminate\Support\Str::slug($service->name);

    // All URLs verified to return image/jpeg 200 from images.pexels.com.
    $map = [
        'phone-unlocking-service'         => 'https://images.pexels.com/photos/4350099/pexels-photo-4350099.jpeg?auto=compress&w=800',
        'app-installation-configuration'  => 'https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&w=800',
        'cloud-setup-sync'                => 'https://images.pexels.com/photos/1092644/pexels-photo-1092644.jpeg?auto=compress&w=800',
        'software-installation-updates'   => 'https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&w=800',
        'virus-malware-removal'           => 'https://images.pexels.com/photos/4350099/pexels-photo-4350099.jpeg?auto=compress&w=800',
        'data-recovery-backup'            => 'https://images.pexels.com/photos/4526481/pexels-photo-4526481.jpeg?auto=compress&w=800',
        'performance-optimization'        => 'https://images.pexels.com/photos/4350099/pexels-photo-4350099.jpeg?auto=compress&w=800',
        'network-connectivity-setup'      => 'https://images.pexels.com/photos/4526481/pexels-photo-4526481.jpeg?auto=compress&w=800',
    ];

    $src = $map[$slug] ?? 'https://images.pexels.com/photos/1092644/pexels-photo-1092644.jpeg?auto=compress&w=800';
    $alt = $service->name;
@endphp
<figure class="relative aspect-[16/10] overflow-hidden rounded-xl bg-paper-alt">
    <img src="{{ $src }}" alt="{{ $alt }}" loading="lazy"
         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
</figure>
