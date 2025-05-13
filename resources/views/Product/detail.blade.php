@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg overflow-hidden">
        <div class="md:flex">
            <!-- Gambar Produk -->
            <div class="md:w-1/2">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                     alt="{{ $product->name }}" 
                     class="w-full h-96 object-cover"
                     onerror="this.src='{{ asset('images/no-image.png') }}'">
            </div>
            
            <!-- Informasi Produk -->
            <div class="md:w-1/2 p-6">
                <div class="flex justify-between items-start">
                    <h1 class="text-3xl font-bold text-gray-800">{{ $product->name }}</h1>
                    <span class="{{ $product->type === 'sale' ? 'bg-blue-500' : 'bg-amber-500' }} text-white px-3 py-1 rounded-full text-sm">
                        {{ $product->type === 'sale' ? 'Beli' : 'Sewa' }}
                    </span>
                </div>
                
                <p class="text-gray-600 mt-4">{{ $product->description }}</p>
                
                <div class="mt-6">
                    <h2 class="text-2xl font-bold text-primary-dark">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                        @if($product->type === 'rent')
                            <span class="text-sm">{{ $product->rent_period }}</span>
                        @endif
                    </h2>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800">Detail Produk</h3>
                    <div class="mt-2 space-y-2">
                        <p><span class="font-medium">Kategori:</span> {{ $product->category ?? 'Tidak ada kategori' }}</p>
                        <p><span class="font-medium">Stok:</span> {{ $product->stock }}</p>
                        @if($product->type === 'rent')
                            <p><span class="font-medium">Periode Sewa:</span> {{ $product->rent_period }}</p>
                        @endif
                    </div>
                </div>

                <div class="mt-8">
                    <button 
                        onclick="handleProductAction('{{ $product->id }}', '{{ $product->type }}')"
                        class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-3 px-6 rounded-lg transition">
                        {{ $product->type === 'sale' ? 'Beli Sekarang' : 'Sewa Sekarang' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Section -->
    <div class="mt-8 bg-white rounded-lg p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Ulasan Produk</h2>
        @if($product->reviews && $product->reviews->count() > 0)
            <div class="space-y-4">
                @foreach($product->reviews as $review)
                    <div class="border-b pb-4">
                        <div class="flex items-center mb-2">
                            <img src="{{ $review->user->profile_photo_url ?? asset('images/no-image.png') }}" 
                                 alt="{{ $review->user->name }}" 
                                 class="w-10 h-10 rounded-full">
                            <div class="ml-3">
                                <p class="font-medium">{{ $review->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <p class="text-gray-600">{{ $review->comment }}</p>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500">Belum ada ulasan untuk produk ini.</p>
        @endif
    </div>
</div>

<script>
function handleProductAction(productId, type) {
    // Implementasi logika pembelian/penyewaan
    alert('Fitur ' + (type === 'sale' ? 'pembelian' : 'penyewaan') + ' akan segera hadir!');
}
</script>
@endsection 