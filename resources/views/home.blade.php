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
</div>
@endsection