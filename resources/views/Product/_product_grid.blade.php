@foreach($products as $product)
<a href="{{ route('products.show', $product->id) }}" class="block">
    <div class="product-card bg-white rounded-lg overflow-hidden shadow-lg transition-transform duration-300 hover:shadow-xl hover:-translate-y-1" data-type="{{ $product->type }}">
        <div class="relative">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}" 
                 alt="{{ $product->name }}" 
                 class="w-full h-48 object-cover lazy"
                 onerror="this.src='{{ asset('images/no-image.png') }}'">
            <span class="absolute top-2 right-2 {{ $product->type === 'sale' ? 'bg-blue-500' : 'bg-amber-500' }} text-white text-xs px-2 py-1 rounded-full">
                {{ $product->type === 'sale' ? 'Beli' : 'Sewa' }}
            </span>
        </div>
        <div class="p-4">
            <h3 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $product->description }}</p>
            <div class="flex justify-between items-center mt-3">
                <span class="text-primary-dark font-bold">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                    @if($product->type === 'rent')
                        <span class="text-sm">{{ $product->rent_period }}</span>
                    @endif
                </span>
                <button 
                    onclick="event.preventDefault(); handleProductAction('{{ $product->id }}', '{{ $product->type }}')"
                    class="bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded-lg text-sm transition">
                    {{ $product->type === 'sale' ? 'Beli' : 'Sewa' }}
                </button>
            </div>
        </div>
    </div>
</a>
@endforeach

@if($products->hasPages())
<div class="col-span-full mt-8">
    {{ $products->links() }}
</div>
@endif 