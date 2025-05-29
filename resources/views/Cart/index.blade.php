@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-8">Keranjang Belanja</h1>

    @if($cartItems->isEmpty())
        <div class="text-center py-8">
            <p class="text-gray-500 mb-4">Keranjang belanja Anda kosong</p>
            <a href="{{ route('products.index') }}" class="inline-block bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-dark">
                Belanja Sekarang
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Daftar Produk -->
            <div class="lg:col-span-2">
                @foreach($cartItems as $item)
                    <div class="bg-white rounded-lg shadow-md p-6 mb-4">
                        <div class="flex items-center">
                            <img src="{{ asset('storage/' . $item->product->image) }}" 
                                 alt="{{ $item->product->name }}" 
                                 class="w-24 h-24 object-cover rounded-lg">
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-semibold">{{ $item->product->name }}</h3>
                                <p class="text-gray-600">{{ $item->product->description }}</p>
                                <div class="mt-2">
                                    <span class="text-primary font-bold">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </span>
                                    @if($item->type === 'rent')
                                        <span class="text-sm text-gray-500">/ {{ $item->rent_period }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center">
                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})" 
                                        class="px-2 py-1 border rounded-l-lg hover:bg-gray-100">-</button>
                                <input type="number" 
                                       value="{{ $item->quantity }}" 
                                       min="1" 
                                       max="{{ $item->product->stock }}"
                                       class="w-16 text-center border-t border-b"
                                       onchange="updateQuantity({{ $item->id }}, this.value)">
                                <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})" 
                                        class="px-2 py-1 border rounded-r-lg hover:bg-gray-100">+</button>
                                <button onclick="removeItem({{ $item->id }})" 
                                        class="ml-4 text-red-500 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Ringkasan Belanja -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-xl font-semibold mb-4">Ringkasan Belanja</h2>
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span>Total Harga</span>
                            <span class="font-bold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <button onclick="checkout()" 
                            class="w-full bg-primary text-white py-3 rounded-lg hover:bg-primary-dark">
                        Checkout
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function updateQuantity(cartId, quantity) {
    if (quantity < 1) return;
    
    fetch(`/cart/${cartId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ quantity: parseInt(quantity) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Jumlah produk berhasil diperbarui',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
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
            text: 'Terjadi kesalahan saat memperbarui jumlah'
        });
    });
}

function removeItem(cartId) {
    Swal.fire({
        title: 'Hapus Item',
        text: 'Apakah Anda yakin ingin menghapus item ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`/cart/${cartId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Item berhasil dihapus',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
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
                    text: 'Terjadi kesalahan saat menghapus item'
                });
            });
        }
    });
}

function checkout() {
    Swal.fire({
        title: 'Alamat Pengiriman',
        input: 'textarea',
        inputLabel: 'Masukkan alamat pengiriman lengkap',
        inputPlaceholder: 'Contoh: Jl. Contoh No. 123, Kota, Provinsi',
        showCancelButton: true,
        confirmButtonText: 'Lanjutkan',
        cancelButtonText: 'Batal',
        inputValidator: (value) => {
            if (!value) {
                return 'Alamat pengiriman harus diisi!';
            }
        }
    }).then((addressResult) => {
        if (addressResult.isConfirmed) {
            Swal.fire({
                title: 'Metode Pembayaran',
                input: 'select',
                inputOptions: {
                    'transfer': 'Transfer Bank',
                    'bank': 'Pembayaran di Bank'
                },
                inputPlaceholder: 'Pilih metode pembayaran',
                showCancelButton: true,
                confirmButtonText: 'Checkout',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Metode pembayaran harus dipilih!';
                    }
                }
            }).then((paymentResult) => {
                if (paymentResult.isConfirmed) {
                    fetch('/checkout', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            shipping_address: addressResult.value,
                            payment_method: paymentResult.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.',
                                confirmButtonText: 'Lihat Pesanan'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = `/orders/${data.order.id}`;
                                }
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
                            text: 'Terjadi kesalahan saat checkout'
                        });
                    });
                }
            });
        }
    });
}
</script>
@endsection 