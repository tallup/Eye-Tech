<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'EyeTech - Makes Your Day Easy')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom Styles -->
    <style>
        :root {
            --primary: #DC2626;
            --primary-dark: #B91C1C;
            --primary-light: #EF4444;
            --secondary: #374151;
            --secondary-dark: #1F2937;
            --white: #FFFFFF;
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
        }

        * {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--secondary-dark);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Liquid Glass Effect */
        .glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        }

        .glass-dark {
            background: rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3);
        }

        /* 3D Card Effects */
        .card-3d {
            transform-style: preserve-3d;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .card-3d:hover {
            transform: translateY(-10px) rotateX(5deg) rotateY(5deg);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        /* Ken Burns Effect */
        .ken-burns {
            animation: kenBurns 20s ease-in-out infinite;
        }

        @keyframes kenBurns {
            0%, 100% { transform: scale(1) translateX(0) translateY(0); }
            25% { transform: scale(1.05) translateX(-2%) translateY(-1%); }
            50% { transform: scale(1.1) translateX(2%) translateY(1%); }
            75% { transform: scale(1.05) translateX(-1%) translateY(2%); }
        }

        /* Parallax Background */
        .parallax-bg {
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }

        /* Floating Animation */
        .floating {
            animation: floating 6s ease-in-out infinite;
        }

        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, var(--primary), var(--primary-light), var(--white));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Glow Effect */
        .glow {
            box-shadow: 0 0 20px rgba(220, 38, 38, 0.5);
            animation: glow 2s ease-in-out infinite alternate;
        }

        @keyframes glow {
            from { box-shadow: 0 0 20px rgba(220, 38, 38, 0.5); }
            to { box-shadow: 0 0 30px rgba(220, 38, 38, 0.8); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #DC2626, #B91C1C);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #B91C1C, #991B1B);
        }

        /* Navigation Styles */
        .nav-link {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .nav-link:hover::before {
            left: 100%;
        }

        /* Mobile Menu Animation */
        .mobile-menu {
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }

        .mobile-menu.open {
            transform: translateX(0);
        }

        /* Loading Animation */
        .loading-dots {
            display: inline-block;
        }

        .loading-dots::after {
            content: '';
            animation: loadingDots 1.5s infinite;
        }

        @keyframes loadingDots {
            0%, 20% { content: ''; }
            40% { content: '.'; }
            60% { content: '..'; }
            80%, 100% { content: '...'; }
        }

        /* Particle Background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: particleFloat 20s infinite linear;
        }

        @keyframes particleFloat {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body style="background: var(--secondary-dark); color: var(--white);">
    <!-- Particle Background -->
    <div class="particles" id="particles"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50" style="background: rgba(31, 41, 55, 0.95); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center group">
                        <img src="{{ asset('images/logo.png') }}" alt="EyeTech Logo" class="w-12 h-12">
                        <span class="ml-3 text-2xl font-bold text-white">EyeTech</span>
                    </a>
                </div>
                
                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}" class="nav-link px-4 py-2 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('home') ? 'text-white bg-red-500/30' : '' }}">
                        Home
                    </a>
                    <a href="{{ route('about') }}" class="nav-link px-4 py-2 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('about') ? 'text-white bg-red-500/30' : '' }}">
                        About
                    </a>
                    <a href="{{ route('services') }}" class="nav-link px-4 py-2 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('services') ? 'text-white bg-red-500/30' : '' }}">
                        Services
                    </a>
                    <a href="{{ route('contact') }}" class="nav-link px-4 py-2 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('contact') ? 'text-white bg-red-500/30' : '' }}">
                        Contact
                    </a>
                    <a href="/admin" class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-700 rounded-lg text-white font-semibold hover:from-red-600 hover:to-red-800 transition-all duration-300 shadow-lg">
                        Admin
                    </a>
                </div>
                
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" class="text-white hover:text-white focus:outline-none" onclick="toggleMobileMenu()">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div id="mobile-menu" class="mobile-menu fixed top-20 right-0 w-80 h-screen md:hidden" style="background: rgba(31, 41, 55, 0.98); backdrop-filter: blur(20px); border-left: 1px solid rgba(255, 255, 255, 0.1);">
            <div class="p-6 space-y-4">
                <a href="{{ route('home') }}" class="block px-4 py-3 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('home') ? 'text-white bg-red-500/30' : '' }}">
                    Home
                </a>
                <a href="{{ route('about') }}" class="block px-4 py-3 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('about') ? 'text-white bg-red-500/30' : '' }}">
                    About
                </a>
                <a href="{{ route('services') }}" class="block px-4 py-3 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('services') ? 'text-white bg-red-500/30' : '' }}">
                    Services
                </a>
                <a href="{{ route('contact') }}" class="block px-4 py-3 rounded-lg text-white hover:text-white hover:bg-red-500/20 transition-all duration-300 {{ request()->routeIs('contact') ? 'text-white bg-red-500/30' : '' }}">
                    Contact
                </a>
                <a href="/admin" class="block px-6 py-3 bg-gradient-to-r from-red-500 to-red-700 rounded-lg text-white font-semibold hover:from-red-600 hover:to-red-800 transition-all duration-300 text-center">
                    Admin Panel
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="pt-20">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="relative mt-20 border-t" style="background: var(--secondary); border-color: rgba(255, 255, 255, 0.1);">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,<svg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><g fill="%23ffffff" fill-opacity="0.1"><circle cx="30" cy="30" r="2"/></g></svg>')"></div>
        </div>
        
        <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="md:col-span-2">
                    <div class="flex items-center mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="EyeTech Logo" class="w-12 h-12">
                        <span class="ml-3 text-2xl font-bold text-white">EyeTech</span>
                    </div>
                    <p class="text-white text-lg mb-6 leading-relaxed">
                        Your trusted partner for mobile phone repairs, accessories, and technical services. 
                        We provide professional solutions with cutting-edge technology and exceptional customer care.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-12 h-12 rounded-lg glass flex items-center justify-center hover:bg-white/20 transition-all duration-300 group">
                            <svg class="w-6 h-6 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-lg glass flex items-center justify-center hover:bg-white/20 transition-all duration-300 group">
                            <svg class="w-6 h-6 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-12 h-12 rounded-lg glass flex items-center justify-center hover:bg-white/20 transition-all duration-300 group">
                            <svg class="w-6 h-6 text-white/70 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.746-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-11.988C24.007 5.367 18.641.001.012.001z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-xl font-bold text-white mb-6">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-white/70 hover:text-white transition-colors duration-300">Home</a></li>
                        <li><a href="{{ route('about') }}" class="text-white/70 hover:text-white transition-colors duration-300">About Us</a></li>
                        <li><a href="{{ route('services') }}" class="text-white/70 hover:text-white transition-colors duration-300">Our Services</a></li>
                        <li><a href="{{ route('contact') }}" class="text-white/70 hover:text-white transition-colors duration-300">Contact Us</a></li>
                        <li><a href="/admin" class="text-white/70 hover:text-white transition-colors duration-300">Admin Panel</a></li>
                    </ul>
                </div>
                
                <!-- Contact Info -->
                <div>
                    <h3 class="text-xl font-bold text-white mb-6">Contact Info</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg glass flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white/70 text-sm">📍 99Q4+GW3, Brikama Hwy, Lamin</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg glass flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white/70 text-sm">📞 382 2063</p>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-lg glass flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-white/70 text-sm">✉️ info@example.com</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="border-t border-white/10 mt-12 pt-8 flex flex-col md:flex-row justify-between items-center">
                <p class="text-white/50 text-sm mb-4 md:mb-0">
                    &copy; {{ date('Y') }} EyeTech. All rights reserved. | Makes Your Day Easy
                </p>
                <div class="flex items-center space-x-6">
                    <span class="text-white/50 text-sm">Built with ❤️ in The Gambia</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script>
        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('open');
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const menuButton = event.target.closest('button[onclick="toggleMobileMenu()"]');
            
            if (!mobileMenu.contains(event.target) && !menuButton) {
                mobileMenu.classList.remove('open');
            }
        });

        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 50;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.width = Math.random() * 4 + 2 + 'px';
                particle.style.height = particle.style.width;
                particle.style.animationDelay = Math.random() * 20 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 10) + 's';
                particlesContainer.appendChild(particle);
            }
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Parallax scrolling effect
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const parallaxElements = document.querySelectorAll('.parallax-bg');
            
            parallaxElements.forEach(element => {
                const speed = 0.5;
                element.style.transform = `translateY(${scrolled * speed}px)`;
            });
        });

        // Initialize particles on page load
        document.addEventListener('DOMContentLoaded', function() {
            createParticles();
        });

        // Add loading animation to buttons
        document.querySelectorAll('button, .btn').forEach(button => {
            button.addEventListener('click', function() {
                if (!this.classList.contains('loading')) {
                    this.classList.add('loading');
                    this.innerHTML = '<span class="loading-dots">Loading</span>';
                    
                    setTimeout(() => {
                        this.classList.remove('loading');
                        this.innerHTML = this.getAttribute('data-original-text') || this.innerHTML;
                    }, 2000);
                }
            });
        });
    </script>
</body>
</html>