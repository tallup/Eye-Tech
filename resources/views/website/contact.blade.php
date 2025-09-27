@extends('layouts.app')

@section('title', 'Contact Us - EyeTech')

@section('content')
<style>
    /* Ensure dropdown options are visible */
    select option {
        background-color: white !important;
        color: #1f2937 !important;
        padding: 8px 12px;
    }
    
    select:focus option {
        background-color: #f9fafb !important;
        color: #1f2937 !important;
    }
    
    /* Style the select element */
    select {
        background-color: white !important;
        color: #1f2937 !important;
    }
</style>

<!-- Hero Section with Background Image -->
<section class="relative min-h-[60vh] flex items-center justify-center overflow-hidden">
    <!-- Background Image -->
    <div class="absolute inset-0 ken-burns parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.8), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');">
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
                Contact <span class="text-red-500">Us</span>
            </h1>
            <p class="text-xl md:text-2xl text-white">
                Get in Touch with EyeTech
            </p>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Contact Details -->
            <div class="bg-gray-800 rounded-lg p-8">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">Get In Touch</h2>
                <p class="text-lg text-gray-200 mb-8">
                    Ready to get your device fixed or need more information about our services? 
                    We're here to help! Contact us using any of the methods below.
                </p>
                
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-1">Address</h3>
                            <p class="text-gray-200">99Q4+GW3, Brikama Hwy, Lamin</p>
                            <p class="text-gray-200">The Gambia</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.949.684V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-1">Phone</h3>
                            <p class="text-gray-200">
                                <a href="tel:3822063" class="hover:text-red-400 transition duration-300">382 2063</a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-1">Business Hours</h3>
                            <p class="text-gray-200">Monday - Saturday: 9:00 AM - 6:00 PM</p>
                            <p class="text-gray-200">Sunday: Closed</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-white mb-1">Email</h3>
                            <p class="text-gray-200">
                                <a href="mailto:info@example.com" class="hover:text-red-400 transition duration-300">info@example.com</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="bg-gray-50 rounded-lg p-8">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>
                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                            <input type="text" id="name" name="name" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                            <input type="tel" id="phone" name="phone" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="service" class="block text-sm font-medium text-gray-700 mb-2">Service Needed</label>
                        <select id="service" name="service" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent bg-white text-gray-900">
                            <option value="" class="text-gray-900">Select a service</option>
                            <option value="software" class="text-gray-900">Software Troubleshooting</option>
                            <option value="hardware" class="text-gray-900">Hardware Repair</option>
                            <option value="data-recovery" class="text-gray-900">Data Recovery</option>
                            <option value="data-transfer" class="text-gray-900">Data Transfer</option>
                            <option value="unlocking" class="text-gray-900">Device Unlocking</option>
                            <option value="other" class="text-gray-900">Other</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="device" class="block text-sm font-medium text-gray-700 mb-2">Device Description</label>
                        <input type="text" id="device" name="device" placeholder="e.g., iPhone 12, Samsung Galaxy S21" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                        <textarea id="message" name="message" rows="4" required 
                                  placeholder="Describe the issue or what you need help with..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-red-600 text-white py-3 px-6 rounded-md font-semibold hover:bg-red-700 transition duration-300 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Find Us</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Visit our shop located on Brikama Highway in Lamin
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="h-96 relative">
                <!-- Google Maps Embed for EyeTech Location -->
                <iframe 
                    src="https://maps.google.com/maps?q=13.3888049,-16.6427274&hl=en&z=15&output=embed"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="rounded-lg">
                </iframe>
                
                <!-- Map Overlay with "Get Directions" button -->
                <div class="absolute bottom-4 right-4">
                    <a href="https://www.google.com/maps/dir/?api=1&destination=13.3888049,-16.6427274&travelmode=driving" 
                       target="_blank" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-blue-700 transition duration-300 text-sm font-medium">
                        Get Directions
                    </a>
                </div>
                
                <!-- Map info overlay -->
                <div class="absolute top-4 left-4 bg-white/90 backdrop-blur rounded-lg px-3 py-2 shadow-lg">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 616 0z"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">EyeTech Location</span>
                    </div>
                </div>
                
                <!-- Fallback content for when map doesn't load -->
                <div class="absolute inset-0 bg-gray-200 flex items-center justify-center map-fallback" style="display: none;">
                <div class="text-center">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                        <p class="text-gray-500">Map loading...</p>
                    <p class="text-sm text-gray-400 mt-2">99Q4+GW3, Brikama Hwy, Lamin</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Common questions about our services and processes
            </p>
        </div>
        
        <div class="space-y-4">
            <!-- FAQ Item 1 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button data-faq-button
                        class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">How long does a typical repair take?</h3>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div data-faq-content class="overflow-hidden" style="display: none;">
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700 leading-relaxed text-base">
                            Most software issues can be resolved within <span class="font-semibold text-red-600">1-2 hours</span>, while hardware repairs may take <span class="font-semibold text-red-600">1-3 days</span> depending on the complexity and parts availability. We'll always provide you with an accurate time estimate before starting any work.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 2 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button data-faq-button
                        class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Do you provide warranty on repairs?</h3>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div data-faq-content class="overflow-hidden" style="display: none;">
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700 leading-relaxed text-base">
                            Yes! We provide a <span class="font-semibold text-red-600">30-day warranty</span> on all repairs and services. If the same issue occurs within this period, we'll fix it <span class="font-semibold text-red-600">free of charge</span>. This gives you peace of mind knowing your device is protected.
                        </p>
                    </div>
                </div>
            </div>

            <!-- FAQ Item 3 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button data-faq-button
                        class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Can you recover data from a completely dead phone?</h3>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div data-faq-content class="overflow-hidden" style="display: none;">
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700 leading-relaxed text-base">
                            We can attempt data recovery from most devices, even those that won't turn on. However, success depends on the extent of damage to the internal components. We use professional data recovery tools and techniques to maximize your chances of retrieving your important files.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Item 4 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button data-faq-button
                        class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Do you work on all phone brands?</h3>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div data-faq-content class="overflow-hidden" style="display: none;">
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700 leading-relaxed text-base">
                            Yes, we service all major brands including <span class="font-semibold text-red-600">iPhone, Samsung, Huawei, Xiaomi, Oppo, OnePlus, Google Pixel</span>, and many others. Our technicians are trained on various platforms and stay updated with the latest repair techniques.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Item 5 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button data-faq-button
                        class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">What payment methods do you accept?</h3>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div data-faq-content class="overflow-hidden" style="display: none;">
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700 leading-relaxed text-base">
                            We accept <span class="font-semibold text-red-600">cash, mobile money, bank transfers, and major credit cards</span>. Payment is due upon completion of the repair, and we'll provide you with a detailed receipt for your records.
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- FAQ Item 6 -->
            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <button data-faq-button
                        class="w-full px-6 py-4 text-left bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-red-500 transition-colors duration-200 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800">Do you offer pickup and delivery services?</h3>
                        <svg class="w-5 h-5 text-gray-500 transform transition-transform duration-200" 
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </div>
                </button>
                <div data-faq-content class="overflow-hidden" style="display: none;">
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <p class="text-gray-700 leading-relaxed text-base">
                            Yes! We offer <span class="font-semibold text-red-600">convenient pickup and delivery services</span> within Lamin and surrounding areas. Contact us to arrange pickup, and we'll bring your device back once the repair is complete.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-20 relative overflow-hidden">
    <!-- Parallax Background -->
    <div class="absolute inset-0 parallax-bg" style="background-image: linear-gradient(135deg, rgba(220, 38, 38, 0.9), rgba(31, 41, 55, 0.9)), url('https://images.unsplash.com/photo-1551434678-e076c223a692?ixlib=rb-4.0.3&auto=format&fit=crop&w=2070&q=80');">
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
                Ready to Get <span class="text-red-500">Started?</span>
            </h2>
            <p class="text-xl md:text-2xl mb-12 text-white max-w-3xl mx-auto leading-relaxed">
                Don't wait! Contact us today for professional mobile device services
            </p>
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                <a href="tel:3822063" class="group relative px-8 py-4 bg-gradient-to-r from-red-500 to-red-700 rounded-xl font-semibold text-white hover:from-red-600 hover:to-red-800 transition-all duration-300 shadow-lg hover:shadow-red-500/25 transform hover:scale-105">
                    <span class="relative z-10">Call Now: 382 2063</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-red-600 to-red-800 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </a>
                <a href="{{ route('services') }}" class="group relative px-8 py-4 bg-transparent border-2 border-white rounded-xl font-semibold text-white hover:bg-white hover:text-red-600 transition-all duration-300 transform hover:scale-105">
                    <span class="relative z-10">View Our Services</span>
                </a>
            </div>
        </div>
    </div>
</section>

    <!-- JavaScript for accordion functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Fallback accordion functionality
            const faqButtons = document.querySelectorAll('[data-faq-button]');
            const faqContents = document.querySelectorAll('[data-faq-content]');
            
            faqButtons.forEach((button, index) => {
                button.addEventListener('click', function() {
                    const content = faqContents[index];
                    const isOpen = content.style.display === 'block';
                    
                    // Close all other accordions
                    faqContents.forEach(c => c.style.display = 'none');
                    faqButtons.forEach(b => {
                        b.querySelector('svg').style.transform = 'rotate(0deg)';
                        b.classList.remove('bg-gray-50');
                        b.classList.add('bg-white');
                    });
                    
                    // Toggle current accordion
                    if (!isOpen) {
                        content.style.display = 'block';
                        button.querySelector('svg').style.transform = 'rotate(180deg)';
                        button.classList.remove('bg-white');
                        button.classList.add('bg-gray-50');
                    }
                });
            });
        });
    </script>
@endsection

