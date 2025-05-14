@extends('app')

@section('content')
<div class="page pt-16 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('monitoring') }}" class="text-primary hover:text-primary-dark flex items-center">
                <i class="fas fa-arrow-left mr-2"></i>
                <span>Kembali ke Monitoring</span>
            </a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-lg">
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Tambah Lahan Baru</h1>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('lands.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="land-name">Nama Lahan</label>
                    <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                           id="land-name" 
                           name="name"
                           type="text" 
                           value="{{ old('name') }}"
                           placeholder="contoh: Kebun Jagung Utara"
                           required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="land-location">Lokasi</label>
                    <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                           id="land-location" 
                           name="location"
                           type="text" 
                           value="{{ old('location') }}"
                           placeholder="contoh: Bogor, Jawa Barat"
                           required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="land-area">Luas Lahan (Ha)</label>
                    <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                           id="land-area" 
                           name="area"
                           type="number" 
                           step="0.1" 
                           value="{{ old('area') }}"
                           placeholder="contoh: 2.5"
                           required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="device-id">ID Perangkat Sensor</label>
                    <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                           id="device-id" 
                           name="device_id"
                           type="text" 
                           value="{{ old('device_id') }}"
                           placeholder="contoh: PLT-SENSOR-001"
                           required>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <a href="{{ route('monitoring') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2">
                        Batal
                    </a>
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 