@extends('layouts.app')

@section('title', 'Our Services - EyeTech')

@section('content')
<!-- Hero Section with Background Image -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 ken-burns parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.8), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');">
        <!-- Floating elements -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-16 h-16 bg-white/10 rounded-full floating" style="animation-delay: 0s;"></div>
            <div class="absolute top-40 right-20 w-12 h-12 bg-red-500/20 rounded-lg floating" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-40 left-20 w-10 h-10 bg-gray-500/20 rounded-full floating" style="animation-delay: 4s;"></div>
            <div class="absolute bottom-20 right-10 w-20 h-20 bg-white/5 rounded-lg floating" style="animation-delay: 6s;"></div>
        </div>
    </div>
    
    <!-- Hero Content -->
    <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <div class="card-3d glass p-12 rounded-3xl" style="background: rgba(31, 41, 55, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2);">
            <h1 class="text-5xl md:text-7xl font-bold mb-8 text-white leading-tight">
                Our <span class="text-red-500">Services</span>
            </h1>
            <p class="text-xl md:text-2xl text-white">
                Professional Software & Technology Solutions
            </p>
        </div>
    </div>
</section>

<!-- Services Grid -->
<section class="py-20 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="20" cy="20" r="1"/></g></svg>')"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                What We <span class="text-red-500">Offer</span>
            </h2>
            <p class="text-xl text-white max-w-3xl mx-auto">
                Comprehensive software and technology services to keep your devices running smoothly
            </p>
        </div>
        
        @if($services->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($services as $service)
            <div class="card-3d glass p-8 rounded-2xl hover:bg-red-500/20 transition-all duration-300 group" style="background: rgba(55, 65, 81, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        @if(str_contains($service->category, 'Software'))
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        @elseif(str_contains($service->category, 'Security'))
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        @elseif(str_contains($service->category, 'Data'))
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                        </svg>
                        @elseif(str_contains($service->category, 'Cloud'))
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        @elseif(str_contains($service->category, 'Network'))
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        @else
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        @endif
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">{{ $service->name }}</h3>
                    <p class="text-white mb-6 leading-relaxed">{{ $service->description }}</p>
                    <div class="flex justify-between items-center mb-4">
                        <div class="text-3xl font-bold text-red-500">D{{ number_format($service->price, 2) }}</div>
                        @if($service->estimated_duration)
                        <div class="text-sm text-white">{{ $service->estimated_duration }} min</div>
                        @endif
                    </div>
                    @if($service->category)
                    <div class="inline-block bg-red-500/20 text-white text-sm px-3 py-1 rounded-full">
                        {{ $service->category }}
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Services Available</h3>
            <p class="text-gray-500">Please check back later for our service offerings.</p>
        </div>
        @endif
    </div>
</section>

<!-- Service Categories -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Service Categories</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                We specialize in various areas of mobile technology services
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Software Services</h3>
                <p class="text-gray-600">App troubleshooting, OS management, virus removal, and system optimization</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Hardware Services</h3>
                <p class="text-gray-600">Screen repairs, battery replacement, charging port fixes, and component upgrades</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Data Recovery</h3>
                <p class="text-gray-600">Recover lost photos, contacts, messages, and important files from damaged devices</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <div class="w-16 h-16 bg-red-100 rounded-lg flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Data Transfer</h3>
                <p class="text-gray-600">Seamless data migration between old and new devices with cloud setup</p>
            </div>
        </div>
    </div>
</section>

<!-- Process Section -->
<section class="py-20 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><polygon points="30,0 45,15 30,30 15,15"/></g></svg>')"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                How It <span class="text-red-500">Works</span>
            </h2>
            <p class="text-xl text-white max-w-3xl mx-auto">
                Simple and straightforward process to get your device fixed
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-700 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold group-hover:scale-110 transition-transform duration-300">
                    1
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Contact Us</h3>
                <p class="text-white leading-relaxed">Call us or visit our shop to describe your device issue</p>
            </div>
            
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-700 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold group-hover:scale-110 transition-transform duration-300">
                    2
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Diagnosis</h3>
                <p class="text-white leading-relaxed">We assess the problem and provide a detailed quote</p>
            </div>
            
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-700 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold group-hover:scale-110 transition-transform duration-300">
                    3
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Repair</h3>
                <p class="text-white leading-relaxed">Our experts perform the necessary repairs or services</p>
            </div>
            
            <div class="text-center group">
                <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-700 text-white rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold group-hover:scale-110 transition-transform duration-300">
                    4
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Testing</h3>
                <p class="text-white leading-relaxed">We test your device to ensure everything works perfectly</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 relative overflow-hidden">
    <!-- Parallax Background -->
    <div class="absolute inset-0 parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.9), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');">
        <!-- Floating elements -->
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/5 rounded-full floating" style="animation-delay: 1s;"></div>
            <div class="absolute top-32 right-16 w-24 h-24 bg-red-500/10 rounded-lg floating" style="animation-delay: 3s;"></div>
            <div class="absolute bottom-32 left-16 w-20 h-20 bg-gray-500/10 rounded-full floating" style="animation-delay: 5s;"></div>
            <div class="absolute bottom-10 right-10 w-28 h-28 bg-white/5 rounded-lg floating" style="animation-delay: 7s;"></div>
        </div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="card-3d glass p-12 rounded-3xl" style="background: rgba(31, 41, 55, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2);">
            <h2 class="text-4xl md:text-6xl font-bold mb-6 text-white">
                Need <span class="text-red-500">Service?</span>
            </h2>
            <p class="text-xl md:text-2xl mb-12 text-white max-w-3xl mx-auto leading-relaxed">
                Contact us today to schedule your device repair or consultation
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="{{ route('contact') }}" class="group relative px-8 py-4 bg-gradient-to-r from-red-500 to-red-700 rounded-xl font-semibold text-white hover:from-red-600 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-red-500/25 transform hover:scale-105">
                    <span class="relative z-10">Contact Us</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-800 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="tel:3822063" class="group relative px-8 py-4 bg-transparent border-2 border-white rounded-xl font-semibold text-white hover:bg-white hover:text-red-600 transition-all duration-300 transform hover:scale-105">
                    <span class="relative z-10">Call: 382 2063</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

