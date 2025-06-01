@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Manajemen Pesanan</h1>

    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Pesanan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->user->name ?? 'Guest' }}</td>
                             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ ucfirst($order->status) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Detail</a>
                                {{-- Add form for status update or deletion if needed --}}
                                <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST" class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">Tidak ada pesanan yang ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Sales Statistics Dashboard --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Total Sales Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-green-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-500 text-white rounded-full p-3">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-semibold text-gray-600">Total Penjualan (Selesai)</h2>
                    <p class="text-2xl font-bold text-green-700 mt-1">Rp {{ number_format($totalSales, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Orders Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border border-blue-100">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-500 text-white rounded-full p-3">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-semibold text-gray-600">Jumlah Pesanan Selesai</h2>
                    <p class="text-2xl font-bold text-blue-700 mt-1">{{ $completedOrdersCount }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Sales Chart Section --}}
    <div class="mt-8 bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-gray-800">Grafik Penjualan</h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.orders.index', ['period' => 'daily']) }}" 
                   class="px-4 py-2 rounded-lg {{ $period === 'daily' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                    Harian
                </a>
                <a href="{{ route('admin.orders.index', ['period' => 'monthly']) }}" 
                   class="px-4 py-2 rounded-lg {{ $period === 'monthly' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                    Bulanan
                </a>
                <a href="{{ route('admin.orders.index', ['period' => 'yearly']) }}" 
                   class="px-4 py-2 rounded-lg {{ $period === 'yearly' ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }} transition-colors">
                    Tahunan
                </a>
            </div>
        </div>
        <div class="h-96">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart').getContext('2d');
    
    const salesData = @json($salesData);
    const labels = @json($labels);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Total Penjualan',
                data: salesData,
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush

@endsection 