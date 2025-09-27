@extends('layouts.app')

@section('title', 'About Us - EyeTech')

@section('content')
<!-- Hero Section with Background Image -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 ken-burns parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.8), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1522202176988-66273c2fd55f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2071&q=80');">
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
                About <span class="text-red-500">EyeTech</span>
            </h1>
            <p class="text-xl md:text-2xl text-white">
                Your Trusted Software & Technology Partner
            </p>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="py-20 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="20" cy="20" r="1"/></g></svg>')"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                    Who <span class="text-red-500">We Are</span>
                </h2>
                <p class="text-lg text-white mb-6 leading-relaxed">
                    EyeTech is a leading software and technology service provider based in Lamin, The Gambia. 
                    We specialize in comprehensive mobile device solutions, from software troubleshooting to 
                    data recovery and system optimization.
                </p>
                <p class="text-lg text-white mb-6 leading-relaxed">
                    Our mission is to make your day easy by providing reliable, professional, and affordable 
                    mobile technology services. We understand how important your mobile devices are to your 
                    daily life, and we're committed to keeping them running smoothly.
                </p>
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-700 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-white">Quality Guaranteed</h3>
                        <p class="text-white">Professional service with satisfaction guarantee</p>
                    </div>
                </div>
            </div>
            
            <div class="glass p-8 rounded-2xl" style="background: rgba(55, 65, 81, 0.8); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1);">
                <h3 class="text-2xl font-bold text-white mb-6">Our Location</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-red-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-white">Address</p>
                            <p class="text-white">99Q4+GW3, Brikama Hwy, Lamin</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-red-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-white">Phone</p>
                            <p class="text-white">382 2063</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <svg class="w-6 h-6 text-red-500 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="font-semibold text-white">Business Hours</p>
                            <p class="text-white">Monday - Saturday: 9:00 AM - 6:00 PM</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">What We Do</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Comprehensive mobile technology services to meet all your needs
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Software Troubleshooting</h3>
                <p class="text-gray-600">Diagnose and fix app crashes, system freezes, and performance issues</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">OS Management</h3>
                <p class="text-gray-600">Operating system updates, reinstalls, and factory resets</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Security Services</h3>
                <p class="text-gray-600">Virus removal, malware protection, and security optimization</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Data Recovery</h3>
                <p class="text-gray-600">Recover lost data from damaged or non-functional devices</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Data Transfer</h3>
                <p class="text-gray-600">Seamless data migration between devices</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">Device Unlocking</h3>
                <p class="text-gray-600">Carrier unlocking and device customization services</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-20 relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><polygon points="30,0 45,15 30,30 15,15"/></g></svg>')"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-6xl font-bold text-white mb-6">
                Our <span class="text-red-500">Commitment</span>
            </h2>
            <p class="text-xl text-white max-w-3xl mx-auto">
                We are dedicated to providing exceptional service and building lasting relationships with our customers
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="text-center group">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Fast Service</h3>
                <p class="text-white leading-relaxed">Quick turnaround times without compromising quality</p>
            </div>
            
            <div class="text-center group">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Quality Assurance</h3>
                <p class="text-white leading-relaxed">Thorough testing and quality checks on all services</p>
            </div>
            
            <div class="text-center group">
                <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-3xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-white mb-4">Customer Care</h3>
                <p class="text-white leading-relaxed">Personalized attention and ongoing support</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 relative overflow-hidden">
    <!-- Parallax Background -->
    <div class="absolute inset-0 parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.9), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1551650975-87deedd944c3?ixlib=rb-4.0.3&auto=format&fit=crop&w=2074&q=80');">
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
                Experience the <span class="text-red-500">EyeTech</span> Difference
            </h2>
            <p class="text-xl md:text-2xl mb-12 text-white max-w-3xl mx-auto leading-relaxed">
                Join thousands of satisfied customers who trust EyeTech for their mobile technology needs
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="{{ route('contact') }}" class="group relative px-8 py-4 bg-gradient-to-r from-red-500 to-red-700 rounded-xl font-semibold text-white hover:from-red-600 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-red-500/25 transform hover:scale-105">
                    <span class="relative z-10">Get Started Today</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-800 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('services') }}" class="group relative px-8 py-4 bg-transparent border-2 border-white rounded-xl font-semibold text-white hover:bg-white hover:text-red-600 transition-all duration-300 transform hover:scale-105">
                    <span class="relative z-10">Our Services</span>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection

