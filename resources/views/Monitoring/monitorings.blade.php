@extends('app')

@section('scripts')
<script>
    // Fungsi untuk animasi smooth
    function animateValue(element, start, end, duration) {
        if (!element) return;
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
        if (!element) return;
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

    function updateLandValues(landId, data) {
        const card = document.querySelector(`[data-land-id="${landId}"]`);
        if (!card) {
            console.error('Card not found for land:', landId);
            return;
        }

        const phValue = card.querySelector('.ph-value');
        const moistureValue = card.querySelector('.moisture-value');
        const phBar = card.querySelector('.ph-bar');
        const moistureBar = card.querySelector('.moisture-bar');

        if (!phValue || !moistureValue || !phBar || !moistureBar) {
            console.error('Required elements not found in card');
            return;
        }
        
        // Animate pH value
        const currentPh = parseFloat(phValue.textContent);
        const newPh = parseFloat(data.ph_value);
        if (!isNaN(currentPh) && !isNaN(newPh)) {
            animateValue(phValue, currentPh, newPh, 1000);
            animateProgressBar(phBar, (currentPh/14)*100, (newPh/14)*100, 1000);
        }
        
        // Animate moisture value
        const currentMoisture = parseInt(moistureValue.textContent);
        const newMoisture = parseInt(data.moisture_value);
        if (!isNaN(currentMoisture) && !isNaN(newMoisture)) {
            animateValue(moistureValue, currentMoisture, newMoisture, 1000);
            animateProgressBar(moistureBar, currentMoisture, newMoisture, 1000);
        }
    }

    const landsData = @json($lands);
    function loadLands() {
        const landsContainer = document.getElementById('lands-container');
        if (!landsContainer) {
            console.error('Lands container not found');
            return;
        }
        landsContainer.innerHTML = '';

        landsData.forEach(land => {
            let landCard = document.createElement('div');
            landCard.className = 'bg-white rounded-lg overflow-hidden shadow-lg mb-4 land-card';
            landCard.setAttribute('data-land-id', land.id);

            const latestData = land.sensor_data[0] || {};

            landCard.innerHTML = `
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">${land.name}</h3>
                        <span class="text-sm text-gray-500">${land.area} Ha</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">${land.location}</p>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">pH Tanah</p>
                            <div class="flex items-center mt-1">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-blue-600 h-2.5 rounded-full ph-bar" style="width: ${(latestData.ph_value || 7)/14*100}%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium ph-value">${latestData.ph_value || 7}</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Kelembapan</p>
                            <div class="flex items-center mt-1">
                                <div class="w-full bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-green-500 h-2.5 rounded-full moisture-bar" style="width: ${latestData.moisture_value || 75}%"></div>
                                </div>
                                <span class="ml-2 text-sm font-medium moisture-value">${latestData.moisture_value || 75}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <a href="/lands/${land.id}" class="bg-primary text-white px-3 py-1 rounded-lg text-sm mr-2">Detail</a>
                        <button onclick="updateLand('${land.id}')" class="bg-blue-500 text-white px-3 py-1 rounded-lg text-sm">Update</button>
                        <button onclick="deleteLand('${land.id}')" class="bg-red-500 text-white px-3 py-1 rounded-lg text-sm">Hapus</button>
                    </div>
                </div>
            `;
            landsContainer.appendChild(landCard);
        });
    }

    function updateLand(landId) {
        fetch(`/lands/${landId}/latest-data`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    updateLandValues(landId, data.data);
                } else {
                    throw new Error(data.message || 'Failed to update land data');
                }
            })
            .catch(error => {
                console.error('Error updating land:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal memperbarui data lahan: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#2E7D32'
                });
            });
    }

    function deleteLand(landId) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            text: 'Apakah Anda yakin ingin menghapus lahan ini?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#2E7D32',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                if (!csrfToken) {
                    Swal.fire({
                        title: 'Error!',
                        text: 'CSRF token tidak ditemukan',
                        icon: 'error',
                        confirmButtonColor: '#2E7D32'
                    });
                    return;
                }

                fetch(`/lands/${landId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const card = document.querySelector(`[data-land-id="${landId}"]`);
                        if (card) {
                            card.remove();
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Lahan berhasil dihapus',
                                icon: 'success',
                                confirmButtonColor: '#2E7D32'
                            });
                        } else {
                            console.error('Card tidak ditemukan setelah penghapusan');
                        }
                    } else {
                        throw new Error(data.message || 'Gagal menghapus lahan');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat menghapus lahan: ' + error.message,
                        icon: 'error',
                        confirmButtonColor: '#2E7D32'
                    });
                });
            }
        });
    }

    // Fungsi untuk update semua lahan
    function updateAllLands() {
        landsData.forEach(land => {
            setTimeout(() => {
                updateLand(land.id);
            }, Math.random() * 1000); 
        });
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadLands();
        
        // Update data setiap 3 detik
        setInterval(updateAllLands, 3600000);
    });
</script>
@endsection

@section('content')
<!-- Monitoring Page -->
<div id="monitoring-page" class="page pt-16 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Monitoring Lahan</h1>
                <p class="text-gray-600">Pantau kondisi lahan Anda secara realtime</p>
            </div>
            <button id="add-land-button" class="bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg">
                <a href="{{ route('lands.create') }}" class="flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    <span>Tambah Lahan</span>
                </a>
            </button>
        </div>
        
        <!-- Lands List -->
        <div id="lands-container" class="mb-12">
            <!-- Lands will be loaded here by JavaScript -->
        </div>
    </div>
</div>
@endsection