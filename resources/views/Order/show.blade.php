@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Detail Pesanan #{{ $order->order_number }}</h1>
                <span class="px-4 py-2 rounded-full text-sm font-medium
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'paid') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-blue-100 text-blue-800
                    @endif">
                    {{ ucfirst($order->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-lg font-semibold mb-2">Informasi Pesanan</h2>
                    <div class="space-y-2">
                        <p><span class="font-medium">Tanggal:</span> {{ $order->created_at->format('d M Y H:i') }}</p>
                        <p><span class="font-medium">Total:</span> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p><span class="font-medium">Metode Pembayaran:</span> {{ ucfirst($order->payment_method) }}</p>
                        <p><span class="font-medium">Alamat Pengiriman:</span> {{ $order->shipping_address }}</p>
                    </div>
                </div>

                @if($order->status === 'pending')
                <div>
                    <h2 class="text-lg font-semibold mb-2">Informasi Pembayaran</h2>
                    <div class="space-y-2">
                        <p><span class="font-medium">Bank:</span> BCA</p>
                        <p><span class="font-medium">No. Rekening:</span> 1234567890</p>
                        <p><span class="font-medium">Atas Nama:</span> Plantify</p>
                        <p><span class="font-medium">Total Transfer:</span> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    </div>
                </div>
                @endif
            </div>

            @if($order->status === 'pending')
            <div class="border-t pt-6">
                <h2 class="text-lg font-semibold mb-4">Upload Bukti Pembayaran</h2>
                <form id="payment-form" class="space-y-4" method="POST" action="{{ route('orders.upload-payment', $order->id) }}">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bukti Transfer
                        </label>
                        <input type="file" 
                               name="payment_proof" 
                               accept="image/*"
                               class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <button type="submit" 
                            class="w-full bg-primary text-white py-2 rounded-lg hover:bg-primary-dark">
                        Upload Bukti Pembayaran
                    </button>
                </form>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Item Pesanan</h2>
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                <div class="flex items-center">
                    <img src="{{ asset('storage/' . $item->product->image) }}" 
                         alt="{{ $item->product->name }}" 
                         class="w-16 h-16 object-cover rounded-lg">
                    <div class="ml-4 flex-1">
                        <h4 class="font-medium">{{ $item->product->name }}</h4>
                        <p class="text-sm text-gray-600">
                            {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                            @if($item->type === 'rent')
                                <span class="text-gray-500">/ {{ $item->rent_period }}</span>
                            @endif
                        </p>
                        @if($item->device_id)
                            <p class="text-sm text-blue-600 font-semibold mt-1">
                                Device ID: <span class="font-normal">{{ $item->device_id }}</span>
                            </p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="font-medium">
                            Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@if($order->status === 'pending')
<script>
document.getElementById('payment-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData();
    const fileInput = form.querySelector('input[name="payment_proof"]');
    
    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Silakan pilih file bukti pembayaran'
        });
        return;
    }

    const file = fileInput.files[0];
    if (file.size > 2 * 1024 * 1024) { // 2MB
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Ukuran file maksimal 2MB'
        });
        return;
    }

    // Check file type if needed, based on your validation rules
    const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!allowedTypes.includes(file.type)) {
         Swal.fire({
             icon: 'error',
             title: 'Oops...',
             text: 'Format file harus JPG, JPEG, atau PNG'
         });
         return;
    }

    formData.append('payment_proof', file);
    // Pastikan Anda memiliki meta tag CSRF token di head HTML
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

    Swal.fire({
        title: 'Mengupload...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Ambil order ID dari URL atau elemen di halaman jika perlu
    // Contoh: let orderId = form.dataset.orderId; // Jika ada data-order-id di form
    // Atau ambil dari URL seperti sebelumnya jika konsisten
    // const orderId = {{ $order->id }};
    // fetch(`/orders/${orderId}/upload-payment`, { ...

    fetch(form.action, { // Menggunakan action URL dari form
        method: form.method, // Menggunakan method dari form
        body: formData
    })
    .then(response => {
        // Periksa apakah respons berstatus 2xx (sukses)
        if (!response.ok) {
            // Jika tidak sukses, coba baca respons sebagai JSON untuk pesan error
            return response.json().then(errorData => {
                throw new Error(errorData.message || 'Terjadi kesalahan pada server');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.reload();
            });
        } else {
             // Ini seharusnya tidak terpanggil jika response.ok, tapi untuk safety
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: data.message || 'Terjadi kesalahan saat memproses upload'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: error.message || 'Terjadi kesalahan jaringan atau server'
        });
    });
});
</script>
@endif
@endsection 