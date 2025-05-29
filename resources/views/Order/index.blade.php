@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-8">Riwayat Transaksi</h1>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $order->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @foreach($order->orderItems as $item)
                                        <div class="mb-1">
                                            {{ $item->quantity }}x {{ $item->product->name }}
                                            @if($item->rent_period)
                                                <span class="text-sm text-gray-500">({{ $item->rent_period }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $order->status === 'processing' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $order->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $order->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($order->status === 'pending')
                                    <button onclick="uploadPayment('{{ $order->id }}')" 
                                            class="text-primary hover:text-primary-dark">
                                        Upload Bukti
                                    </button>
                                @else
                                    <a href="{{ route('orders.show', $order->id) }}" class="text-primary hover:text-primary-dark">Detail</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                Belum ada transaksi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function uploadPayment(orderId) {
    Swal.fire({
        title: 'Upload Bukti Pembayaran',
        html: `
            <div class="text-center">
                <div class="mb-4">
                    <i class="fas fa-upload text-5xl text-primary"></i>
                </div>
                <p class="text-gray-600 mb-4">Silakan upload bukti pembayaran Anda</p>
                <input type="file" id="payment-proof" class="w-full" accept="image/*">
                <p class="text-sm text-gray-500 mt-2">Format: JPG, PNG (Max. 2MB)</p>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Upload',
        cancelButtonText: 'Batal',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            const fileInput = document.getElementById('payment-proof');
            if (!fileInput.files.length) {
                Swal.showValidationMessage('Pilih file terlebih dahulu');
                return false;
            }
            const file = fileInput.files[0];
            if (file.size > 2 * 1024 * 1024) {
                Swal.showValidationMessage('Ukuran file maksimal 2MB');
                return false;
            }
            if (!['image/jpeg', 'image/png'].includes(file.type)) {
                Swal.showValidationMessage('Format file harus JPG atau PNG');
                return false;
            }
            return file;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append('payment_proof', result.value);
            formData.append('_token', '{{ csrf_token() }}');

            Swal.fire({
                title: 'Mengupload...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/orders/${orderId}/upload-payment`, {
                method: 'POST',
                body: formData
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
                    text: 'Terjadi kesalahan saat mengupload file'
                });
            });
        }
    });
}
</script>
@endsection 