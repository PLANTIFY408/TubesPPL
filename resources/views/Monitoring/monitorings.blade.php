@extends('app')

@section('scripts')
<script>

    const sampleLand = [
        { id: 1, name: "Lahan Kebun Utara", location: "Bogor, Jawa Barat", area: "2.5 Ha", ph: 6.8, moisture: 75 },
        { id: 2, name: "Sawah Timur", location: "Malang, Jawa Timur", area: "1.2 Ha", ph: 7.2, moisture: 82 }
    ];

    function addNewLand() {
        const landForm = document.getElementById('add-land-form');
        landForm.classList.toggle('hidden');
    }

    function loadLands() {
            const landsContainer = document.getElementById('lands-container');
            landsContainer.innerHTML = ''; // Clear existing content first
            
            sampleLand.forEach(land => {
                const landCard = document.createElement('div');
                landCard.className = 'bg-white rounded-lg overflow-hidden shadow-lg mb-4';
                
                landCard.innerHTML = `
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800">${land.name}</h3>
                            <span class="text-sm text-gray-500">${land.area}</span>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">${land.location}</p>
                    </div>
                    <div class="p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">pH Tanah</p>
                                <div class="flex items-center mt-1">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-blue-600 h-2.5 rounded-full" style="width: ${(land.ph/14)*100}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm font-medium">${land.ph}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Kelembapan</p>
                                <div class="flex items-center mt-1">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-green-500 h-2.5 rounded-full" style="width: ${land.moisture}%"></div>
                                    </div>
                                    <span class="ml-2 text-sm font-medium">${land.moisture}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button class="bg-primary text-white px-3 py-1 rounded-lg text-sm mr-2">Detail</button>
                            <button class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm">Update</button>
                        </div>
                    </div>
                `;
                
                landsContainer.appendChild(landCard);
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            loadLands();
        });
</script>
@endsection

@section('content')
<!-- Monitoring Page -->
<div id="monitoring-page" class="page pt-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Monitoring Lahan</h1>
                    <p class="text-gray-600">Pantau kondisi lahan Anda secara realtime</p>
                </div>
                <button onclick="addNewLand()" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Tambah Lahan</span>
                </button>
            </div>
            
            <!-- Add New Land Form -->
            <div id="add-land-form" class="bg-white p-6 rounded-lg shadow-lg mb-8 hidden">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Tambah Lahan Baru</h3>
                <form class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="land-name">Nama Lahan</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" id="land-name" type="text" placeholder="contoh: Kebun Jagung Utara">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="land-location">Lokasi</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" id="land-location" type="text" placeholder="contoh: Bogor, Jawa Barat">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="land-area">Luas Lahan (Ha)</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" id="land-area" type="number" step="0.1" placeholder="contoh: 2.5">
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="device-id">ID Perangkat Sensor</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" id="device-id" type="text" placeholder="contoh: PLT-SENSOR-001">
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button onclick="addNewLand(); return false;" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2">
                            Batal
                        </button>
                        <button class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Lands List -->
            <div id="lands-container" class="mb-12">
                <!-- Lands will be loaded here by JavaScript -->
            </div>
        </div>
    </div>
@endsection