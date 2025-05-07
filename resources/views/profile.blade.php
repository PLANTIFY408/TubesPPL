@extends('app')

@section('content')
<div id="profile-page" class="page pt-24 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
                <div class="relative h-48 bg-primary">
                    <div class="absolute -bottom-16 left-8">
                        <div class="w-32 h-32 rounded-full border-4 border-white overflow-hidden">
                            <img src="{{ $user->profile_photo_url ?? '/api/placeholder/200/200' }}" alt="Profile" class="w-full h-full object-cover">
                        </div>
                    </div>
                </div>
                <div class="pt-20 px-8 pb-8">
                    <div class="flex justify-between items-start">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-800">{{ $user->name }}</h1>
                            <p class="text-gray-600">{{ $user->role ?? 'Petani' }}</p>
                            <div class="flex mt-2 items-center text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt text-primary mr-1"></i>
                                <span>{{ $user->address ?? 'Belum diatur' }}</span>
                            </div>
                        </div>
                        <button class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition-colors">
                            Edit Profil
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Account Information -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="p-4 bg-primary text-white">
                        <h2 class="text-lg font-semibold">Informasi Akun</h2>
                    </div>
                    <div class="p-4">
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500">Email</h3>
                            <p class="text-gray-800">{{ $user->email }}</p>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500">No. Telepon</h3>
                            <p class="text-gray-800">{{ $user->phone ?? 'Belum diatur' }}</p>
                        </div>
                        <div class="mb-4">
                            <h3 class="text-sm font-medium text-gray-500">Tipe Akun</h3>
                            <p class="text-gray-800">{{ $user->account_type ?? 'Basic' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Anggota Sejak</h3>
                            <p class="text-gray-800">{{ $user->created_at->format('d F Y') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="p-4 bg-primary text-white">
                        <h2 class="text-lg font-semibold">Statistik</h2>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-3xl font-bold text-primary">{{ $user->active_farms ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Lahan Aktif</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-3xl font-bold text-primary">{{ $user->devices_count ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Perangkat</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-3xl font-bold text-primary">{{ $user->consultations_count ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Konsultasi</div>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <div class="text-3xl font-bold text-primary">{{ $user->purchases_count ?? 0 }}</div>
                                <div class="text-sm text-gray-500">Produk Dibeli</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                    <div class="p-4 bg-primary text-white">
                        <h2 class="text-lg font-semibold">Aksi Cepat</h2>
                    </div>
                    <div class="p-4">
                        <div class="grid gap-3">
                            <a href="{{ route('monitoring') }}" class="flex items-center text-left p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <i class="fas fa-chart-line text-primary mr-3"></i>
                                <span>Lihat Dashboard</span>
                            </a>
                            <a href="#" class="flex items-center text-left p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <i class="fas fa-history text-primary mr-3"></i>
                                <span>Riwayat Transaksi</span>
                            </a>
                            <a href="#" class="flex items-center text-left p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <i class="fas fa-bell text-primary mr-3"></i>
                                <span>Notifikasi</span>
                            </a>
                            <a href="#" class="flex items-center text-left p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                                <i class="fas fa-cog text-primary mr-3"></i>
                                <span>Pengaturan</span>
                            </a>
                            <form action="{{ route('logout') }}" method="POST" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center text-left p-3 bg-red-100 hover:bg-red-200 rounded-lg transition-colors">
                                    <i class="fas fa-sign-out-alt text-red-600 mr-3"></i>
                                    <span class="text-red-600">Keluar</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection