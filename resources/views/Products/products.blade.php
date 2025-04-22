@extends('app')

@section('scripts')
<script>
    // Data produk
    window.sampleProducts = [
        { id: 1, name: "Bibit Jagung Premium", price: 25000, type: "sale", image: "/api/placeholder/300/200", description: "Bibit jagung berkualitas tinggi, tumbuh subur dalam 14 hari." },
        { id: 2, name: "Pupuk Organik", price: 75000, type: "sale", image: "/api/placeholder/300/200", description: "Pupuk organik 5kg, cocok untuk berbagai jenis tanaman." },
        { id: 3, name: "Traktor Mini", price: 250000, type: "rent", rentPeriod: "per minggu", image: "/api/placeholder/300/200", description: "Traktor mini untuk lahan kecil dan menengah." },
        { id: 4, name: "Alat Penyiram Otomatis", price: 125000, type: "rent", rentPeriod: "per bulan", image: "/api/placeholder/300/200", description: "Sistem penyiraman otomatis dengan timer dan sensor kelembapan." },
        { id: 5, name: "Benih Sayur Paket Komplit", price: 45000, type: "sale", image: "/api/placeholder/300/200", description: "Paket benih sayuran: bayam, kangkung, sawi, dan selada." },
        { id: 6, name: "Greenhouse Portable", price: 500000, type: "rent", rentPeriod: "per bulan", image: "/api/placeholder/300/200", description: "Greenhouse portable ukuran 3x4m, mudah dipasang dan dibongkar." }
    ];

    // Fungsi filter
    window.toggleProductFilter = function(type) {
        console.log('Filtering products:', type); // Debug log
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            if (btn.dataset.filter === type) {
                btn.classList.add('bg-primary', 'text-white');
                btn.classList.remove('bg-gray-200', 'text-gray-700');
            } else {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            }
        });

        const products = document.querySelectorAll('.product-card');
        products.forEach(product => {
            if (type === 'all' || product.dataset.type === type) {
                product.classList.remove('hidden');
            } else {
                product.classList.add('hidden');
            }
        });
    }

    // Fungsi untuk memuat produk
    function loadProducts() {
        console.log('Loading products...'); // Debug log
        const productsContainer = document.getElementById('products-container');
        if (!productsContainer) {
            console.error('Products container not found!'); // Debug log
            return;
        }
        
        productsContainer.innerHTML = ''; // Clear existing content
        
        window.sampleProducts.forEach(product => {
            console.log('Adding product:', product.name); // Debug log
            const productCard = document.createElement('div');
            productCard.className = 'product-card bg-white rounded-lg overflow-hidden shadow-lg transition-transform duration-300 hover:shadow-xl hover:-translate-y-1';
            productCard.dataset.type = product.type;
            
            const badge = product.type === 'sale' ? 
                '<span class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded-full">Beli</span>' : 
                '<span class="absolute top-2 right-2 bg-amber-500 text-white text-xs px-2 py-1 rounded-full">Sewa</span>';
            
            const priceDisplay = product.type === 'rent' ? 
                `Rp ${product.price.toLocaleString('id-ID')} <span class="text-sm">${product.rentPeriod}</span>` : 
                `Rp ${product.price.toLocaleString('id-ID')}`;
            
            productCard.innerHTML = `
                <div class="relative">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-48 object-cover">
                    ${badge}
                </div>
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">${product.name}</h3>
                    <p class="text-sm text-gray-600 mt-1">${product.description}</p>
                    <div class="flex justify-between items-center mt-3">
                        <span class="text-primary-dark font-bold">${priceDisplay}</span>
                        <button class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded-lg text-sm transition">
                            ${product.type === 'sale' ? 'Beli' : 'Sewa'}
                        </button>
                    </div>
                </div>
            `;
            
            productsContainer.appendChild(productCard);
        });
        console.log('Products loaded successfully!'); // Debug log
    }

    // Event listener untuk memuat produk
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Products page DOM Content Loaded'); // Debug log
        loadProducts(); // Load products immediately
    });

    // Event listener untuk showPage
    if (typeof showPage === 'function') {
        const originalShowPage = showPage;
        showPage = function(pageId) {
            originalShowPage(pageId);
            if (pageId === 'products-page') {
                console.log('Products page shown, loading products...'); // Debug log
                loadProducts();
            }
        };
    }
</script>
@endsection

@section('content')
<div id="products-page" class="page min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Produk Pertanian</h1>
        <p class="text-gray-600 mb-8">Dapatkan produk berkualitas untuk kebutuhan pertanian Anda</p>
        
        <!-- Filter -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn bg-primary text-white px-4 py-2 rounded-lg text-sm font-medium" data-filter="all" onclick="toggleProductFilter('all')">Semua</button>
                <button class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium" data-filter="sale" onclick="toggleProductFilter('sale')">Barang Dijual</button>
                <button class="filter-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium" data-filter="rent" onclick="toggleProductFilter('rent')">Barang Disewa</button>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div id="products-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Products will be loaded here by JavaScript -->
        </div>
    </div>
</div>
@endsection