@extends('app')

@section('content')
<div id="profile-page" class="page pt-20 min-h-screen pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-8">
            <div class="relative h-48 bg-primary">
                <div class="absolute -bottom-16 left-8">
                    <div class="w-32 h-32 rounded-full border-4 border-white overflow-hidden">
                        <img src="{{ asset('storage/' . ($user->profile_photo_path ?? 'no-image.png')) }}" 
                             alt="Profile" 
                             class="w-full h-full object-cover">
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
                    <button onclick="openEditModal()" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg transition-colors">
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
                            <div class="text-3xl font-bold text-primary">{{ $user->lands()->count() }}</div>
                            <div class="text-sm text-gray-500">Lahan Aktif</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-primary">{{ $totalTools }}</div>
                            <div class="text-sm text-gray-500">Alat Dimiliki</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-primary">{{ $chattedExpertsCount }}</div>
                            <div class="text-sm text-gray-500">Konsultasi</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-lg">
                            <div class="text-3xl font-bold text-primary">
                                {{ $user->orders()->where('status', '!=', 'cancelled')->with('orderItems')->get()->sum(function($order) {
                                    return $order->orderItems->sum('quantity');
                                }) }}
                            </div>
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
                        <a href="{{ route('orders.index') }}" class="flex items-center text-left p-3 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                            <i class="fas fa-history text-primary mr-3"></i>
                            <span>Riwayat Transaksi</span>
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

<!-- Edit Profile Modal -->
<div id="edit-profile-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Edit Profil</h3>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="name">Nama</label>
                    <input type="text" name="name" id="name" value="{{ $user->name }}" 
                           class="border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                    <input type="email" name="email" id="email" value="{{ $user->email }}" 
                           class="border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">No. Telepon</label>
                    <input type="text" name="phone" id="phone" value="{{ $user->phone }}" 
                           class="border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="address">Alamat</label>
                    <textarea name="address" id="address" 
                              class="border rounded w-full py-2 px-3 text-gray-700 leading-tight">{{ $user->address }}</textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="current_password">Password Saat Ini</label>
                    <input type="password" name="current_password" id="current_password" 
                           class="border rounded w-full py-2 px-3 text-gray-700 leading-tight ">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password">Password Baru</label>
                    <input type="password" name="new_password" id="new_password" 
                           class="border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="new_password_confirmation">Konfirmasi Password Baru</label>
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                           class="border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="profile_photo">Foto Profil</label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="border rounded w-full py-2 px-3 text-gray-700 leading-tight">
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeEditModal()" 
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-xl focus:outline-none focus:shadow-outline">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-xl focus:outline-none focus:shadow-outline">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openEditModal() {
    document.getElementById('edit-profile-modal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('edit-profile-modal').classList.add('hidden');
}
</script>
@endsection