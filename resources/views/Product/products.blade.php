@extends('app')

@section('scripts')
<script>
    // Fungsi untuk menangani aksi produk (beli/sewa)
    function handleProductAction(productId, type) {
        // Cek apakah user sudah login
        @guest
            Swal.fire({
                title: 'Login Diperlukan',
                html: `
                    <div class="text-center">
                        <div class="mb-4">
                            <i class="fas fa-user-lock text-5xl text-primary"></i>
                        </div>
                        <p class="text-gray-600 mb-4">Anda harus login terlebih dahulu untuk melanjutkan</p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Login',
                cancelButtonText: 'Daftar',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#2E7D32'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route('login') }}';
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    window.location.href = '{{ route('register') }}';
                }
            });
            return;
        @endguest

        // Tampilkan popup untuk input quantity menggunakan SweetAlert2
        Swal.fire({
            title: 'Masukkan Jumlah',
            input: 'number',
            inputLabel: 'Jumlah yang ingin dibeli',
            inputAttributes: {
                min: 1,
                step: 1
            },
            showCancelButton: true,
            confirmButtonText: 'Lanjutkan',
            cancelButtonText: 'Batal',
            showLoaderOnConfirm: true,
            preConfirm: (quantity) => {
                if (!quantity || quantity < 1) {
                    Swal.showValidationMessage('Mohon masukkan jumlah yang valid');
                    return false;
                }
                return quantity;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const quantity = result.value;
                
                // Jika tipe sewa, tampilkan popup untuk periode sewa
                if (type === 'rent') {
                    Swal.fire({
                        title: 'Periode Sewa',
                        input: 'text',
                        inputLabel: 'Masukkan periode sewa (contoh: 1 bulan)',
                        showCancelButton: true,
                        confirmButtonText: 'Lanjutkan',
                        cancelButtonText: 'Batal',
                        showLoaderOnConfirm: true,
                        preConfirm: (rentPeriod) => {
                            if (!rentPeriod) {
                                Swal.showValidationMessage('Mohon masukkan periode sewa');
                                return false;
                            }
                            return rentPeriod;
                        }
                    }).then((rentResult) => {
                        if (rentResult.isConfirmed) {
                            addToCart(productId, quantity, type, rentResult.value);
                        }
                    });
                } else {
                    addToCart(productId, quantity, type);
                }
            }
        });
    }

    // Fungsi untuk menambahkan ke keranjang
    function addToCart(productId, quantity, type, rentPeriod = null) {
        fetch(`/cart/${productId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                quantity: parseInt(quantity),
                type: type,
                rent_period: rentPeriod
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = '/cart';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: data.message || 'Terjadi kesalahan'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Terjadi kesalahan saat menambahkan ke keranjang'
            });
        });
    }

    // Fungsi filter
    function toggleProductFilter(type) {
        // Update tampilan button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.dataset.filter === type) {
                btn.classList.add('bg-primary', 'text-white');
                btn.classList.remove('bg-gray-200', 'text-gray-700');
            } else {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            }
        });

        // Update URL
        const url = new URL(window.location);
        if (type === 'all') {
            url.searchParams.delete('type');
        } else {
            url.searchParams.set('type', type);
        }
        window.location.href = url.toString();
    }

    // Event listener untuk pencarian
    document.addEventListener('DOMContentLoaded', function() {
        const searchForm = document.getElementById('search-form');
        const searchInput = document.getElementById('search-input');
        const clearSearchBtn = document.getElementById('clear-search');

        // Fungsi untuk melakukan pencarian
        function performSearch() {
            console.log('Performing search with:', searchInput.value); // Debug log
            const url = new URL(window.location);
            const searchTerm = searchInput.value.trim();
            
            if (searchTerm) {
                url.searchParams.set('search', searchTerm);
            } else {
                url.searchParams.delete('search');
            }
            
            console.log('Redirecting to:', url.toString()); // Debug log
            window.location.href = url.toString();
        }

        // Event listener untuk form submit
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            console.log('Form submitted'); // Debug log
            performSearch();
        });

        // Event listener untuk input pencarian
        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 500);
        });

        // Event listener untuk tombol clear
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', function() {
                searchInput.value = '';
                performSearch();
            });
        }

        // Event listener untuk sorting
        document.getElementById('sort-select').addEventListener('change', function(e) {
            const [sort, order] = e.target.value.split('-');
            const url = new URL(window.location);
            url.searchParams.set('sort', sort);
            url.searchParams.set('order', order);
            window.location.href = url.toString();
        });

        // Set filter button state berdasarkan URL
        const url = new URL(window.location);
        const type = url.searchParams.get('type') || 'all';
        document.querySelectorAll('.filter-btn').forEach(btn => {
            if (btn.dataset.filter === type) {
                btn.classList.add('bg-primary', 'text-white');
                btn.classList.remove('bg-gray-200', 'text-gray-700');
            } else {
                btn.classList.remove('bg-primary', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            }
        });
    });
</script>
@endsection

@section('content')
<div id="products-page" class="page min-h-screen py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Produk Pertanian</h1>
        <p class="text-gray-600 mb-8">Dapatkan produk berkualitas untuk kebutuhan pertanian Anda</p>
        
        <!-- Search and Sort -->
        <div class="mb-8 flex flex-wrap gap-4 items-center">
            <form id="search-form" method="GET" action="/products" class="flex-1 flex gap-2">
                <div class="flex-1 relative">
                    <input type="text" 
                           id="search-input" 
                           name="search"
                           placeholder="Cari produk..." 
                           value="{{ request('search') }}" 
                           class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-primary focus:ring-1 focus:ring-primary">
                    @if(request('search'))
                        <button type="button" 
                                id="clear-search"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    @endif
                </div>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark">
                    Cari
                </button>
            </form>
        </div>
        
        <!-- Filter -->
        <div class="mb-8">
            <div class="flex flex-wrap gap-2">
                <button class="filter-btn {{ !request('type') || request('type') == 'all' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg text-sm font-medium" data-filter="all" onclick="toggleProductFilter('all')">Semua</button>
                <button class="filter-btn {{ request('type') == 'sale' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg text-sm font-medium" data-filter="sale" onclick="toggleProductFilter('sale')">Barang Dijual</button>
                <button class="filter-btn {{ request('type') == 'rent' ? 'bg-primary text-white' : 'bg-gray-200 text-gray-700' }} px-4 py-2 rounded-lg text-sm font-medium" data-filter="rent" onclick="toggleProductFilter('rent')">Barang Disewa</button>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @include('Product._product_grid')
        </div>
    </div>
</div>
@endsection