@php
    $service = $service ?? null;
    if (! $service) return;
    $slug = \Illuminate\Support\Str::slug($service->name);

    // Each entry is either:
    //   ['img', '<pexels-url>']  — verified image/jpeg 200
    //   ['tile', '<short label>'] — brand-red text tile fallback
    //
    // All Pexels URLs verified: curl -sI -L "<url>" -w '%{content_type} %{http_code}'
    // returned "image/jpeg 200" on 2026-05-23.
    $map = [
        'phone-unlocking-service'        => ['img',  'https://images.pexels.com/photos/4974915/pexels-photo-4974915.jpeg?auto=compress&w=800'],
        'app-installation-configuration' => ['img',  'https://images.pexels.com/photos/230544/pexels-photo-230544.jpeg?auto=compress&w=800'],
        'cloud-setup-sync'               => ['img',  'https://images.pexels.com/photos/3680219/pexels-photo-3680219.jpeg?auto=compress&w=800'],
        'software-installation-updates'  => ['img',  'https://images.pexels.com/photos/4974916/pexels-photo-4974916.jpeg?auto=compress&w=800'],
        'virus-malware-removal'          => ['img',  'https://images.pexels.com/photos/4068316/pexels-photo-4068316.jpeg?auto=compress&w=800'],
        'data-recovery-backup'           => ['img',  'https://images.pexels.com/photos/4068317/pexels-photo-4068317.jpeg?auto=compress&w=800'],
        'performance-optimization'       => ['img',  'https://images.pexels.com/photos/3568521/pexels-photo-3568521.jpeg?auto=compress&w=800'],
        'network-connectivity-setup'     => ['img',  'https://images.pexels.com/photos/3568523/pexels-photo-3568523.jpeg?auto=compress&w=800'],
    ];

    $entry = $map[$slug] ?? ['img', 'https://images.pexels.com/photos/4974920/pexels-photo-4974920.jpeg?auto=compress&w=800'];
    [$type, $value] = $entry;
@endphp
@if ($type === 'img')
<figure class="relative aspect-[16/10] overflow-hidden rounded-xl bg-paper-alt">
    <img src="{{ $value }}" alt="{{ $service->name }}" loading="lazy"
         class="absolute inset-0 h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
</figure>
@else
<figure class="relative aspect-[16/10] overflow-hidden rounded-xl bg-brand-red text-white flex flex-col justify-between p-5">
    <p class="text-xs uppercase tracking-widest opacity-80">EyeTech service</p>
    <p class="font-display text-xl leading-tight">{{ $value }}</p>
</figure>
@endif
