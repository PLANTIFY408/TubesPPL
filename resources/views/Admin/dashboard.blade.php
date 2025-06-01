@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Admin Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Contoh Card Statistik -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-lg font-semibold text-gray-700">Total User</div>
            <div class="text-3xl font-bold text-primary mt-2">{{ \App\Models\User::count() }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-lg font-semibold text-gray-700">Total Produk</div>
            <div class="text-3xl font-bold text-primary mt-2">{{ \App\Models\Product::count() }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-lg font-semibold text-gray-700">Total Transaksi</div>
            <div class="text-3xl font-bold text-primary mt-2">{{ \App\Models\Order::count() }}</div>
        </div>
    </div>

    <!-- Tambahkan tautan ke halaman manajemen lainnya -->
    <div class="mt-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Manajemen</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('admin.users.index') }}" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg text-center">Manage User</a>
            <a href="{{ route('admin.products.index') }}" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-lg text-center">Manage Produk</a>
            <a href="{{ route('admin.orders.index') }}" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-lg text-center">Manage Transaksi</a>
        </div>
    </div>

</div>
@endsection 