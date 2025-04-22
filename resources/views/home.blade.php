@extends('app')

@section('content')
<!-- Home Page -->
<div id="home-page" class="page min-h-screen">
    <div class="relative">
        <!-- Background Image -->
        <div class="absolute inset-0">
            <img src="{{ asset('images/banner1.png') }}" alt="Background" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-primary opacity-60"></div>
        </div>

        <!-- Content -->
        <div class="relative max-w-7xl mx-auto px-4 py-16 sm:px-6 lg:px-8 lg:py-20">
            <div class="lg:grid lg:grid-cols-2 lg:gap-8 items-center">
                <div class="mb-8 lg:mb-0">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white">Solusi Pertanian Modern untuk Hasil Panen Optimal</h1>
                    <p class="mt-4 text-lg text-green-100">Plantify membantu Anda mengelola lahan pertanian dengan teknologi monitoring realtime, konsultasi ahli, dan akses ke produk pertanian berkualitas.</p>
                    <div class="mt-8 flex space-x-4">
                        <button onclick="showPage('products-page')" class="bg-white text-primary hover:bg-gray-100 font-bold py-2 px-6 rounded-lg transition-colors">Lihat Produk</button>
                        <button onclick="showPage('monitoring-page')" class="border-2 border-white text-white hover:bg-white hover:text-primary font-bold py-2 px-6 rounded-lg transition-colors">Mulai Monitoring</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800">Fitur Unggulan Plantify</h2>
            <p class="mt-4 text-lg text-gray-600">Tingkatkan produktivitas lahan Anda dengan fitur modern</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 rounded-full bg-primary-light bg-opacity-20 flex items-center justify-center mb-4">
                    <i class="fas fa-chart-line text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Monitoring Realtime</h3>
                <p class="text-gray-600">Pantau kondisi lahan Anda kapan saja dan di mana saja dengan data pH dan kelembapan tanah secara realtime.</p>
                <button onclick="showPage('monitoring-page')" class="mt-4 text-primary hover:text-primary-dark flex items-center">
                    <span>Mulai Monitoring</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 rounded-full bg-primary-light bg-opacity-20 flex items-center justify-center mb-4">
                    <i class="fas fa-shopping-cart text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Produk Berkualitas</h3>
                <p class="text-gray-600">Dapatkan akses ke produk pertanian berkualitas tinggi, baik untuk dibeli maupun disewa sesuai kebutuhan Anda.</p>
                <button onclick="showPage('products-page')" class="mt-4 text-primary hover:text-primary-dark flex items-center">
                    <span>Lihat Produk</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white p-6 rounded-lg shadow-lg hover:shadow-xl transition-shadow">
                <div class="w-12 h-12 rounded-full bg-primary-light bg-opacity-20 flex items-center justify-center mb-4">
                    <i class="fas fa-comments text-primary text-xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Konsultasi Ahli</h3>
                <p class="text-gray-600">Konsultasikan masalah pertanian Anda langsung dengan para ahli tanaman yang berpengalaman di bidangnya.</p>
                <button onclick="showPage('consultation-page')" class="mt-4 text-primary hover:text-primary-dark flex items-center">
                    <span>Konsultasi Sekarang</span>
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-bold text-primary">1,200+</div>
                    <div class="mt-2 text-gray-600">Petani Aktif</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-primary">5,000+</div>
                    <div class="mt-2 text-gray-600">Hektar Lahan</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-primary">95%</div>
                    <div class="mt-2 text-gray-600">Kepuasan Pengguna</div>
                </div>
                <div>
                    <div class="text-3xl font-bold text-primary">24/7</div>
                    <div class="mt-2 text-gray-600">Monitoring</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Section -->
    <div class="max-w-7xl mx-auto px-4 py-12 sm:px-6 lg:px-8 lg:py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800">Testimoni Pengguna</h2>
            <p class="mt-4 text-lg text-gray-600">Pendapat mereka yang telah menggunakan Plantify</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('images/UlasanCow1.jpg') }}" alt="User Avatar" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Hadi Suryana</h4>
                        <p class="text-sm text-gray-600">Petani Sayur, Bandung</p>
                    </div>
                </div>
                <p class="text-gray-600">"Berkat monitoring Plantify, saya dapat mengontrol kelembapan tanah dengan tepat. Hasilnya panen sayur saya meningkat hingga 30% dari biasanya."</p>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('images/UlasanCe.jpeg') }}" alt="User Avatar" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Siti Aminah</h4>
                        <p class="text-sm text-gray-600">Petani Buah, Malang</p>
                    </div>
                </div>
                <p class="text-gray-600">"Fitur konsultasi ahli sangat membantu saya mengatasi hama pada tanaman jeruk. Sekarang pohon jeruk saya berbuah lebat dan berkualitas."</p>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white p-6 rounded-lg shadow-lg">
                <div class="flex items-center mb-4">
                    <img src="{{ asset('images/UlasanCow2.jpg') }}" alt="User Avatar" class="w-12 h-12 rounded-full mr-4">
                    <div>
                        <h4 class="font-semibold text-gray-800">Bambang Wijaya</h4>
                        <p class="text-sm text-gray-600">Petani Padi, Surabaya</p>
                    </div>
                </div>
                <p class="text-gray-600">"Aplikasi ini memudahkan saya menyewa alat pertanian tanpa harus keluar banyak modal. Sangat praktis dan biaya sewa sangat terjangkau."</p>
            </div>
        </div>
    </div>
</div>
@endsection
