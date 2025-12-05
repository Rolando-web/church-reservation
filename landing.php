<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Church Reservation System - Welcome</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero-section {
            background: linear-gradient(rgba(0, 43, 92, 0.7), rgba(0, 43, 92, 0.8)), 
                        url('assets/images/church.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
        }
        
        .feature-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            transition: all 0.3s ease;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .scroll-indicator {
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="fixed w-full z-50 bg-white/90 backdrop-blur-md shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <span class="text-3xl">🕊️</span>
                    <span class="ml-2 text-md font-serif font-light text-primary">Church Reservation</span>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#home" class="text-gray-700 hover:text-primary transition px-3 py-2">Home</a>
                    <a href="#features" class="text-gray-700 hover:text-primary transition px-3 py-2">Features</a>
                    <a href="#about" class="text-gray-700 hover:text-primary transition px-3 py-2">About</a>
                    <a href="#contact" class="text-gray-700 hover:text-primary transition px-3 py-2">Contact</a>
                    <a href="login.php" class="text-primary border border-blue-900 hover:bg-blue-900 hover:text-white transition px-6 py-2 rounded-lg font-semibold">Login</a>
                    <a href="register.php" class="hover:text-blue-900 hover:bg-white hover:border border-blue-900 text-white bg-blue-900 transition px-6 py-2 rounded-lg font-semibold">Get Started</a>
                </div>
                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button id="mobile-menu-btn" class="text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="px-4 py-3 space-y-3">
                <a href="#home" class="block text-gray-700 hover:text-primary">Home</a>
                <a href="#features" class="block text-gray-700 hover:text-primary">Features</a>
                <a href="#about" class="block text-gray-700 hover:text-primary">About</a>
                <a href="#contact" class="block text-gray-700 hover:text-primary">Contact</a>
                <a href="login.php" class="block btn-secondary text-center">Login</a>
                <a href="register.php" class="block btn-primary text-center">Get Started</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section id="home" class="hero-section flex items-center justify-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center fade-in-up">
            <div class="text-7xl mb-6">🕊️</div>
            <h1 class="text-5xl md:text-6xl font-serif font-bold text-white mb-6">
                Celebrate Your Sacred Moments with Us
            </h1>
            <p class="text-xl md:text-2xl text-white/90 mb-8 max-w-2xl mx-auto">
                Book weddings, baptisms, and other sacred ceremonies with ease and reverence. Your unforgettable moment is just a few clicks away.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-12">
                <a href="register.php" class="btn-primary text-lg px-8 py-4 inline-block">
                    Book a Ceremony
                </a>
                <a href="login.php" class="bg-white text-primary px-8 py-4 rounded-lg font-semibold hover:bg-gray-100 transition inline-block">
                    Sign In
                </a>
            </div>
            <div class="scroll-indicator">
                <svg class="w-8 h-8 mx-auto text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-serif font-bold text-primary mb-4">Sacred Services We Offer</h2>
                <p class="text-xl text-gray-700 max-w-2xl mx-auto">
                    Book your special moment in our beautiful church with comprehensive services
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card p-8 rounded-2xl border-2 border-[#002B5C] shadow-md hover:shadow-xl transition-all">
                    <div class="text-5xl mb-4">💒</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Wedding Ceremonies</h3>
                    <p class="text-gray-700">
                        Celebrate your union in God's presence. Complete wedding packages with priest, music, decoration, and lifetime memories.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card p-8 rounded-2xl border-2 border-[#002B5C] shadow-md hover:shadow-xl transition-all">
                    <div class="text-5xl mb-4">👼</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Baptism & Christening</h3>
                    <p class="text-gray-700">
                        Welcome your child into the Christian faith. Sacred baptism ceremonies with clergy, certificates, and family celebration space.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card p-8 rounded-2xl border-2 border-[#002B5C] shadow-md hover:shadow-xl transition-all">
                    <div class="text-5xl mb-4">🕊️</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Funeral Services</h3>
                    <p class="text-gray-700">
                        Honor your loved ones with dignity. Complete funeral arrangements with clergy, viewing area, and memorial service coordination.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card p-8 rounded-2xl border-2 border-[#002B5C] shadow-md hover:shadow-xl transition-all">
                    <div class="text-5xl mb-4">📿</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">First Communion</h3>
                    <p class="text-gray-700">
                        Milestone sacrament for young believers. Preparation classes, ceremony coordination, and family celebration arrangements.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card p-8 rounded-2xl border-2 border-[#002B5C] shadow-md hover:shadow-xl transition-all">
                    <div class="text-5xl mb-4">✝️</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Confirmation</h3>
                    <p class="text-gray-700">
                        Strengthen your faith journey. Complete confirmation programs with bishop visits, preparation sessions, and ceremonies.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card p-8 rounded-2xl border-2 border-[#002B5C] shadow-md hover:shadow-xl transition-all">
                    <div class="text-5xl mb-4">🎄</div>
                    <h3 class="text-2xl font-semibold text-gray-900 mb-3">Special Events</h3>
                    <p class="text-gray-700">
                        Host religious gatherings and celebrations. Christmas programs, Easter services, prayer meetings, and community events.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-20 bg-[#002B5C]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-serif font-bold text-white mb-4">Easy Booking Process</h2>
                <p class="text-xl text-white/90 max-w-2xl mx-auto">
                    Reserve your sacred moment in just four simple steps
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="text-center">
                    <div class="w-24 h-24 bg-white text-primary rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-lg">
                        1
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Create Account</h3>
                    <p class="text-white/90">Quick registration with your email and basic information</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center">
                    <div class="w-24 h-24 bg-white text-primary rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-lg">
                        2
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Browse Services</h3>
                    <p class="text-white/90">Explore our church services with photos and detailed pricing</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-24 h-24 bg-white text-primary rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-lg">
                        3
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Submit Request</h3>
                    <p class="text-white/90">Fill out reservation form and wait for admin approval</p>
                </div>

                <!-- Step 4 -->
                <div class="text-center">
                    <div class="w-24 h-24 bg-white text-primary rounded-full flex items-center justify-center text-3xl font-bold mx-auto mb-4 shadow-lg">
                        4
                    </div>
                    <h3 class="text-xl font-semibold mb-2 text-white">Pay & Confirm</h3>
                    <p class="text-white/90">Complete secure payment and receive instant confirmation</p>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-serif font-bold text-primary mb-6">Experience Sacred Moments</h2>
                    <p class="text-lg text-gray-700 mb-4">
                        Our Church Reservation System brings together tradition and technology to make booking church services seamless. Whether you're planning a wedding, baptism, funeral, or any sacred ceremony, we provide a dignified and efficient booking experience.
                    </p>
                    <p class="text-lg text-gray-700 mb-4">
                        With stunning church facilities, experienced clergy, comprehensive service packages, and transparent pricing, we ensure your special moments are celebrated with the reverence they deserve.
                    </p>
                    <p class="text-lg text-gray-700 mb-6">
                        Browse our beautiful church spaces with photos, compare service packages, and book online 24/7. Join hundreds of families who trust us for their most important life events.
                    </p>
                    <a href="register.php" class="btn-primary inline-block">
                        Start Your Journey Today
                    </a>
                </div>
                <div class="bg-gray-50 rounded-2xl shadow-xl p-8 border-2 border-[#002B5C]">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-6">Why Families Choose Us</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><strong>Beautiful Facilities:</strong> Historic church with stained glass windows and elegant interiors</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><strong>Experienced Clergy:</strong> Compassionate priests with decades of ceremonial expertise</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><strong>Complete Packages:</strong> Music, decorations, certificates, and refreshments included</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><strong>Transparent Pricing:</strong> View all costs upfront with detailed service descriptions</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><strong>Flexible Scheduling:</strong> Weekend and weekday availability to suit your needs</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-3 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-gray-700"><strong>Digital Convenience:</strong> Online booking, instant receipts, and status tracking</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-[#002B5C] text-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-serif font-bold mb-6">Begin Your Sacred Journey Today</h2>
            <p class="text-xl mb-8 text-white/95">
                Create an account now and discover our beautiful church services with transparent pricing. Your perfect ceremony awaits.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="register.php" class="bg-white text-primary px-10 py-4 rounded-lg font-bold text-lg hover:bg-gray-100 transition shadow-lg inline-block">
                    Create Free Account →
                </a>
                <a href="login.php" class="border-2 border-white text-white px-10 py-4 rounded-lg font-bold text-lg hover:bg-white hover:text-primary transition inline-block">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-serif font-bold text-primary mb-4">Visit Our Church</h2>
                <p class="text-xl text-gray-700">
                    We're here to help you plan your perfect ceremony
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="bg-gray-50 p-6 rounded-xl shadow-lg border-2 border-[#002B5C]">
                    <div class="text-4xl mb-3">📍</div>
                    <h3 class="font-semibold text-gray-900 mb-2">Address</h3>
                    <p class="text-gray-700">123 Church Street<br>City, Country</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl shadow-lg border-2 border-[#002B5C]">
                    <div class="text-4xl mb-3">📞</div>
                    <h3 class="font-semibold text-gray-900 mb-2">Phone</h3>
                    <p class="text-gray-700">(123) 456-7890</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-xl shadow-lg border-2 border-[#002B5C]">
                    <div class="text-4xl mb-3">✉️</div>
                    <h3 class="font-semibold text-gray-900 mb-2">Email</h3>
                    <p class="text-gray-700">info@church.com</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-white py-12 bg-[#002B5C]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center mb-4">
                        <span class="text-3xl">🕊️</span>
                        <span class="ml-2 text-xl font-serif font-bold">Church Reservation</span>
                    </div>
                    <p class="text-gray-400">
                        Simplifying church bookings for everyone.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Quick Links</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#home" class="hover:text-white transition">Home</a></li>
                        <li><a href="#features" class="hover:text-white transition">Features</a></li>
                        <li><a href="#about" class="hover:text-white transition">About</a></li>
                        <li><a href="#contact" class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Account</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="login.php" class="hover:text-white transition">Login</a></li>
                        <li><a href="register.php" class="hover:text-white transition">Register</a></li>
                        <li><a href="forgot-password.php" class="hover:text-white transition">Forgot Password</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Support</h4>
                    <ul class="space-y-2 text-gray-400">
                        <li><a href="#" class="hover:text-white transition">Help Center</a></li>
                        <li><a href="#" class="hover:text-white transition">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white transition">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-gray-400">
                <p>&copy; 2025 Church Reservation System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.classList.add('shadow-lg');
            } else {
                nav.classList.remove('shadow-lg');
            }
        });
    </script>
</body>
</html>
