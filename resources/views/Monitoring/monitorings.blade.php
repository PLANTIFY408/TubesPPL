@extends('app')

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    // Inisialisasi Pusher
    const pusher = new Pusher('{{ config('broadcasting.connections.pusher.key') }}', {
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}'
    });

    // Subscribe ke channel monitoring
    const channel = pusher.subscribe('monitoring');

    // Fungsi untuk animasi smooth
    function animateValue(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = start + (end - start) * progress;
            element.textContent = typeof end === 'number' ? currentValue.toFixed(1) : Math.round(currentValue);
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    // Fungsi untuk animasi progress bar
    function animateProgressBar(element, start, end, duration) {
        let startTimestamp = null;
        const step = (timestamp) => {
            if (!startTimestamp) startTimestamp = timestamp;
            const progress = Math.min((timestamp - startTimestamp) / duration, 1);
            const currentValue = start + (end - start) * progress;
            element.style.width = `${currentValue}%`;
            if (progress < 1) {
                window.requestAnimationFrame(step);
            }
        };
        window.requestAnimationFrame(step);
    }

    function addNewLand() {
        const landForm = document.getElementById('add-land-form');
        landForm.classList.toggle('hidden');
    }

    function updateLandValues(landId, data) {
        const card = document.querySelector(`[data-land-id="${landId}"]`);
        if (!card) return;

        const phValue = card.querySelector('.ph-value');
        const moistureValue = card.querySelector('.moisture-value');
        const phBar = card.querySelector('.ph-bar');
        const moistureBar = card.querySelector('.moisture-bar');
        
        // Animate pH value
        animateValue(phValue, parseFloat(phValue.textContent), data.ph_value, 1000);
        animateProgressBar(phBar, (parseFloat(phValue.textContent)/14)*100, (data.ph_value/14)*100, 1000);
        
        // Animate moisture value
        animateValue(moistureValue, parseInt(moistureValue.textContent), data.moisture_value, 1000);
        animateProgressBar(moistureBar, parseInt(moistureValue.textContent), data.moisture_value, 1000);
    }

    function loadLands() {
        const landsContainer = document.getElementById('lands-container');
        landsContainer.innerHTML = ''; // Clear existing content first
        
        @foreach($lands as $land)
            const landCard = document.createElement('div');
            landCard.className = 'bg-white rounded-lg overflow-hidden shadow-lg mb-4 land-card';
            landCard.setAttribute('data-land-id', '{{ $land->id }}');
            
            const latestData = @json($land->sensorData->first());
            
            landCard.innerHTML = `
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $land->name }}</h3>
                        <span class="text-sm text-gray-500">{{ $land->area }} Ha</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">{{ $land->location }}</p>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">pH Tanah</p>
                            <div class="flex items-center mt-1">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full ph-bar" style="width: ${(latestData?.ph_value || 7)/14*100}%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium ph-value">${latestData?.ph_value || 7}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kelembapan</p>
                            <div class="flex items-center mt-1">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-500 h-2.5 rounded-full moisture-bar" style="width: ${latestData?.moisture_value || 75}%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium moisture-value">${latestData?.moisture_value || 75}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('lands.show', $land->id) }}" class="bg-primary text-white px-3 py-1 rounded-lg text-sm mr-2">Detail</a>
                        <button onclick="updateLand('{{ $land->id }}')" class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm">Update</button>
                    </div>
                </div>
            `;
            
            landsContainer.appendChild(landCard);
        @endforeach
    }

    function updateLand(landId) {
        fetch(`/lands/${landId}/latest-data`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateLandValues(landId, data.data);
                }
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Listen untuk event sensor update
    channel.bind('sensor-update', function(data) {
        updateLandValues(data.land_id, data);
    });
    
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
            <form id="land-form" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @csrf
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="land-name">Nama Lahan</label>
                    <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                           id="land-name" 
                           name="name"
                           type="text" 
                           placeholder="contoh: Kebun Jagung Utara"
                           required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="land-location">Lokasi</label>
                    <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                           id="land-location" 
                           name="location"
                           type="text" 
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
                           placeholder="contoh: 2.5"
                           required>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="device-id">ID Perangkat Sensor</label>
                    <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                           id="device-id" 
                           name="device_id"
                           type="text" 
                           placeholder="contoh: PLT-SENSOR-001"
                           required>
                </div>
                <div class="md:col-span-2 flex justify-end">
                    <button type="button" onclick="addNewLand()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2">
                        Batal
                    </button>
                    <button type="submit" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg">
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

<script>
document.getElementById('land-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('lands.store') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            addNewLand(); // Hide form
            loadLands(); // Reload lands
        } else {
            // Handle validation errors
            const errors = data.errors;
            Object.keys(errors).forEach(key => {
                const input = document.querySelector(`[name="${key}"]`);
                if (input) {
                    input.classList.add('border-red-500');
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'text-red-500 text-xs mt-1';
                    errorDiv.textContent = errors[key][0];
                    input.parentNode.appendChild(errorDiv);
                }
            });
        }
    })
    .catch(error => console.error('Error:', error));
});
</script>
@endsection