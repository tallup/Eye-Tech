@extends('layouts.app')

@section('title', 'EyeTech - Makes Your Day Easy')

@section('content')
<!-- Hero Section with Ken Burns Effect -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 ken-burns parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.8), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1512941937669-90a1b58e7e9c?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');">
        <!-- Floating geometric shapes -->
        <div class="absolute inset-0">
            <div class="absolute top-20 left-10 w-20 h-20 bg-white/10 rounded-full floating" style="animation-delay: 0s;"></div>
            <div class="absolute top-40 right-20 w-16 h-16 bg-red-500/20 rounded-lg floating" style="animation-delay: 2s;"></div>
            <div class="absolute bottom-40 left-20 w-12 h-12 bg-gray-500/20 rounded-full floating" style="animation-delay: 4s;"></div>
            <div class="absolute bottom-20 right-10 w-24 h-24 bg-white/5 rounded-lg floating" style="animation-delay: 6s;"></div>
        </div>
    </div>
    
    <!-- Hero Content -->
    <div class="relative z-10 text-center px-4 sm:px-6 lg:px-8 max-w-6xl mx-auto">
        <div class="card-3d glass p-12 rounded-3xl" style="background: rgba(31, 41, 55, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2);">
            <h1 class="text-6xl md:text-8xl font-bold mb-8 text-white leading-tight">
                EyeTech
            </h1>
            <p class="text-2xl md:text-4xl font-light mb-8 text-white">
                Makes Your Day Easy
            </p>
            <p class="text-lg md:text-xl mb-12 text-white max-w-4xl mx-auto leading-relaxed">
                Your trusted partner for mobile phone repairs, accessories, and technical services. 
                We provide professional solutions with cutting-edge technology and exceptional customer care.
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="{{ route('services') }}" class="group relative px-8 py-4 bg-gradient-to-r from-red-500 to-red-700 rounded-xl font-semibold text-white hover:from-red-600 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-red-500/25 transform hover:scale-105">
                    <span class="relative z-10">Our Services</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-800 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('contact') }}" class="group px-8 py-4 glass border-2 border-white/20 rounded-xl font-semibold text-white hover:bg-white/10 hover:border-white/40 transition-all duration-300 transform hover:scale-105">
                    Contact Us
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
        </svg>
    </div>
</section>

<!-- Services Section -->
<section class="py-20 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="20" cy="20" r="1"/></g></svg>')"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Our <span class="text-red-500">Services</span>
            </h2>
            <p class="text-xl text-white max-w-3xl mx-auto">
                Comprehensive mobile phone and accessories services to keep your devices running smoothly
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($services as $service)
            <div class="card-3d glass p-8 rounded-2xl hover:bg-red-500/20 transition-all duration-300 group" style="background: rgba(55, 65, 81, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                <div class="text-center">
                    <div class="w-20 h-20 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-4">{{ $service->name }}</h3>
                    <p class="text-white mb-6 leading-relaxed">{{ Str::limit($service->description, 120) }}</p>
                    <div class="flex justify-between items-center">
                        <div class="text-3xl font-bold text-red-500">D{{ number_format($service->price, 2) }}</div>
                        <div class="text-sm text-white">{{ $service->estimated_duration }} min</div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-16">
                <div class="glass p-12 rounded-2xl">
                    <p class="text-white/50 text-xl">No services available at the moment.</p>
                </div>
            </div>
            @endforelse
        </div>
        
        <div class="text-center mt-16">
            <a href="{{ route('services') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-500 to-red-700 rounded-xl font-semibold text-white hover:from-red-600 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-red-500/25 transform hover:scale-105">
                View All Services
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                </svg>
            </a>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
@if($featuredProducts->count() > 0)
<section class="py-20 relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Featured <span class="text-red-500">Products</span>
            </h2>
            <p class="text-xl text-white max-w-3xl mx-auto">
                Quality mobile phones and accessories at competitive prices
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($featuredProducts as $product)
            <div class="card-3d glass overflow-hidden rounded-2xl hover:bg-red-500/20 transition-all duration-300 group" style="background: rgba(55, 65, 81, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                <!-- Product Image -->
                <div class="h-64 bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-500/20 to-gray-500/20"></div>
                    @if($product->image && file_exists(public_path('storage/' . $product->image)))
                        <img src="{{ asset('storage/' . $product->image) }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <!-- Fallback content (hidden by default) -->
                        <div class="absolute inset-0 flex items-center justify-center" style="display: none;">
                            <div class="text-center">
                                <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-white text-sm font-medium">{{ $product->brand }} {{ $product->model }}</p>
                            </div>
                        </div>
                    @else
                        <div class="relative z-10 text-center">
                            <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-white text-sm font-medium">{{ $product->brand }} {{ $product->model }}</p>
                            <p class="text-white/70 text-xs mt-1">No image available</p>
                        </div>
                    @endif
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-white mb-3">{{ $product->name }}</h3>
                    <p class="text-white mb-4 text-sm leading-relaxed">{{ Str::limit($product->description, 80) }}</p>
                    <div class="flex justify-between items-center">
                        <div class="text-2xl font-bold text-red-500">D{{ number_format($product->selling_price, 2) }}</div>
                        <div class="text-sm text-white bg-red-500/20 px-3 py-1 rounded-full">Stock: {{ $product->stock_quantity }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Why Choose Us Section -->
<section class="py-20 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><polygon points="30,0 45,15 30,30 15,15"/></g></svg>')"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Why Choose <span class="text-red-500">EyeTech?</span>
            </h2>
            <p class="text-xl text-white max-w-3xl mx-auto">
                We are committed to providing exceptional service and quality products
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <div class="text-center group">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Quality Service</h3>
                <p class="text-white leading-relaxed">Professional technicians with years of experience and state-of-the-art equipment</p>
            </div>
            
            <div class="text-center group">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Fast Turnaround</h3>
                <p class="text-white leading-relaxed">Quick repairs and efficient service delivery with same-day options available</p>
            </div>
            
            <div class="text-center group">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Competitive Prices</h3>
                <p class="text-white leading-relaxed">Affordable solutions without compromising on quality or service standards</p>
            </div>
            
            <div class="text-center group">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Customer Care</h3>
                <p class="text-white leading-relaxed">Dedicated support and customer satisfaction with warranty on all services</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 relative overflow-hidden">
    <!-- Animated Background -->
    <div class="absolute inset-0 parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.9), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');">
        <!-- Floating elements -->
        <div class="absolute inset-0">
            <div class="absolute top-10 left-10 w-32 h-32 bg-white/5 rounded-full floating" style="animation-delay: 1s;"></div>
            <div class="absolute top-32 right-16 w-24 h-24 bg-red-500/10 rounded-lg floating" style="animation-delay: 3s;"></div>
            <div class="absolute bottom-32 left-16 w-20 h-20 bg-gray-500/10 rounded-full floating" style="animation-delay: 5s;"></div>
            <div class="absolute bottom-10 right-10 w-28 h-28 bg-white/5 rounded-lg floating" style="animation-delay: 7s;"></div>
        </div>
    </div>
    
    <div class="relative z-10 max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <div class="card-3d glass p-12 rounded-3xl" style="background: rgba(31, 41, 55, 0.9); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.2);">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Ready to Get <span class="text-red-500">Started?</span>
            </h2>
            <p class="text-xl md:text-2xl mb-12 text-white leading-relaxed">
                Contact us today for all your mobile phone and accessories needs. 
                We're here to make your day easy!
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="{{ route('contact') }}" class="group relative px-8 py-4 bg-gradient-to-r from-red-500 to-red-700 rounded-xl font-semibold text-white hover:from-red-600 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-red-500/25 transform hover:scale-105">
                    <span class="relative z-10">Contact Us Now</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-800 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="tel:3822063" class="group px-8 py-4 glass border-2 border-white/20 rounded-xl font-semibold text-white hover:bg-white/10 hover:border-white/40 transition-all duration-300 transform hover:scale-105">
                    📞 Call: 382 2063
                </a>
            </div>
        </div>
    </div>
</section>
@endsection