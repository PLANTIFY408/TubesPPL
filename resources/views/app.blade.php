<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantify - Solusi Pertanian Modern</title>
    <link rel="website icon" type="png" href="{{ asset('images/plantify_icon.png') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#2E7D32',
                            light: '#4CAF50',
                            dark: '#1B5E20'
                        },
                        secondary: {
                            DEFAULT: '#795548',
                            light: '#A1887F',
                            dark: '#5D4037'
                        }
                    }
                }
            }
        }
    </script>
    @yield('scripts')
    <style>
        /* Custom animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }
        .gradient-bg {
            background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
        }

        /* Pastikan halaman login/register memenuhi tinggi layar */
        #login-page {
            min-height: 100vh;
        }

        /* Pastikan konten utama memiliki tinggi minimum */
        .content-wrapper {
            min-height: calc(100vh - 64px); /* 64px adalah tinggi navbar */
        }
    </style>
</head>
<body class="bg-gray-100 font-sans">
    <div class="flex flex-col min-h-screen">
        <!-- Navbar Ribka -->
        <nav class="bg-white w-full z-10">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 flex items-center">
                            <img src="{{ asset('images/plantify_logo-shadow.png') }}" alt="Plantify Logo" class="h-8">
                        </div>
                        <div class="hidden md:ml-6 md:flex md:space-x-8">
                            <a href="{{ route('home') }}" class="nav-item text-gray-600 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors" data-target="home-page">Beranda</a>
                            <a href="{{ route('products') }}" class="nav-item text-gray-600 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors" data-target="products-page">Produk</a>
                            @auth
                                <a href="{{ route('monitoring') }}" class="nav-item text-gray-600 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors" data-target="monitoring-page">Monitoring</a>
                                <a href="{{ route('consultation') }}" class="nav-item text-gray-600 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors" data-target="consultation-page">Konsultasi</a>
                            @endauth
                        </div>
                    </div>
                    <div class="flex items-center">
                        @guest
                            <div id="auth-buttons" class="flex space-x-2">
                                <a href="{{ route('login') }}" class="bg-transparent hover:bg-primary-light text-primary hover:text-white border border-primary hover:border-transparent rounded-md px-3 py-1 text-sm transition-colors">Masuk</a>
                                <a href="{{ route('register') }}" class="bg-primary hover:bg-primary-dark text-white rounded-md px-3 py-1 text-sm transition-colors">Daftar</a>
                            </div>
                        @else
                            <div id="user-profile-btn" class="flex items-center space-x-4">
                                <div class="relative">
                                    <a href="{{ route('profile') }}" class="flex items-center focus:outline-none">
                                        <img class="h-8 w-8 rounded-full border-2 border-primary" src="{{ Auth::user()->profile_photo_url ?? '/api/placeholder/100/100' }}" alt="Profile">
                                        <span class="nav-item text-gray-600 hover:text-primary px-3 py-2 rounded-md text-sm font-medium transition-colors">{{ Auth::user()->name }}</span>
                                    </a>
                                </div>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="nav-item text-gray-600 hover:text-primary py-2 rounded-md text-sm font-medium transition-colors">Keluar</button>
                                </form>
                            </div>
                        @endguest
                        <div class="ml-4 md:hidden flex items-center">
                            <button onclick="toggleMobileMenu()" class="text-gray-500 hover:text-primary">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="md:hidden hidden animate-fade-in">
                <div class="px-2 pt-2 pb-3 space-y-1 bg-white shadow-lg">
                    <a href="{{ route('home') }}" class="nav-item block text-gray-600 hover:text-primary px-3 py-2 rounded-md text-base font-medium">Beranda</a>
                    <a href="{{ route('products') }}" class="nav-item block text-gray-600 hover:text-primary px-3 py-2 rounded-md text-base font-medium">Produk</a>
                    @auth
                        <a href="{{ route('monitoring') }}" class="nav-item block text-gray-600 hover:text-primary px-3 py-2 rounded-md text-base font-medium">Monitoring</a>
                        <a href="{{ route('consultation') }}" class="nav-item block text-gray-600 hover:text-primary px-3 py-2 rounded-md text-base font-medium" >Konsultasi</a>
                        <a href="{{ route('profile') }}" class="nav-item block text-gray-600 hover:text-primary px-3 py-2 rounded-md text-base font-medium" >Profil</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Content -->
        <div class="flex-grow content-wrapper">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <div>
                        <div class="flex items-center mb-4">
                            <img src="{{ asset('images/plantify_footer.png') }}" alt="Plantify Logo" class="h-8">
                        </div>
                        <p class="text-gray-400 mb-4">Solusi pertanian modern untuk hasil panen optimal.</p>
                        <div class="flex space-x-4">
                            <a href="#" class="text-gray-400 hover:text-primary-light">
                                <i class="fab fa-facebook"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary-light">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary-light">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="#" class="text-gray-400 hover:text-primary-light">
                                <i class="fab fa-youtube"></i>
                            </a>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Fitur</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Monitoring Lahan</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Produk Pertanian</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Konsultasi Ahli</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Laporan & Analisis</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Perusahaan</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">Tentang Kami</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Karir</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Blog</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Kontak</a></li>
                        </ul>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold mb-4">Bantuan</h3>
                        <ul class="space-y-2">
                            <li><a href="#" class="text-gray-400 hover:text-white">FAQ</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Pusat Bantuan</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Syarat & Ketentuan</a></li>
                            <li><a href="#" class="text-gray-400 hover:text-white">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                </div>

                <div class="border-t border-gray-700 mt-8 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400">&copy; 2025 Plantify. Hak Cipta Dilindungi.</p>
                    <div class="mt-4 md:mt-0">
                        <a href="#" class="text-gray-400 hover:text-white mr-4">Kebijakan Privasi</a>
                        <a href="#" class="text-gray-400 hover:text-white">Syarat Penggunaan</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</body>
</html>
