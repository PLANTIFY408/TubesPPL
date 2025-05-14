@extends('app')

@section('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>

    // Data untuk chart
    let phChart, moistureChart, tempChart, humidityChart;
    const maxDataPoints = 20; // Jumlah data point yang ditampilkan di chart

    function initCharts() {
        // pH Chart
        const phCtx = document.getElementById('phChart').getContext('2d');
        phChart = new Chart(phCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'pH Tanah',
                    data: [],
                    borderColor: 'rgb(59, 130, 246)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        max: 14
                    }
                }
            }
        });

        // Moisture Chart
        const moistureCtx = document.getElementById('moistureChart').getContext('2d');
        moistureChart = new Chart(moistureCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Kelembapan Tanah (%)',
                    data: [],
                    borderColor: 'rgb(34, 197, 94)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        max: 100
                    }
                }
            }
        });

        // Temperature Chart
        const tempCtx = document.getElementById('tempChart').getContext('2d');
        tempChart = new Chart(tempCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Suhu (°C)',
                    data: [],
                    borderColor: 'rgb(239, 68, 68)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true
            }
        });

        // Humidity Chart
        const humidityCtx = document.getElementById('humidityChart').getContext('2d');
        humidityChart = new Chart(humidityCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Kelembapan Udara (%)',
                    data: [],
                    borderColor: 'rgb(168, 85, 247)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        min: 0,
                        max: 100
                    }
                }
            }
        });
    }

    function updateCharts(data) {
        const timestamp = new Date(data.timestamp).toLocaleTimeString();

        // Update pH Chart
        updateChart(phChart, timestamp, data.ph_value);

        // Update Moisture Chart
        updateChart(moistureChart, timestamp, data.moisture_value);

        // Update Temperature Chart
        updateChart(tempChart, timestamp, data.temperature);

        // Update Humidity Chart
        updateChart(humidityChart, timestamp, data.humidity);

        // Update current values dengan animasi
        animateValue('currentPh', data.ph_value.toFixed(1));
        animateValue('currentMoisture', data.moisture_value + '%');
        animateValue('currentTemp', data.temperature + '°C');
        animateValue('currentHumidity', data.humidity + '%');

        // Update last update time
        document.getElementById('lastUpdate').textContent = 'Baru saja diperbarui';
    }

    function updateChart(chart, label, value) {
        chart.data.labels.push(label);
        chart.data.datasets[0].data.push(value);

        if (chart.data.labels.length > maxDataPoints) {
            chart.data.labels.shift();
            chart.data.datasets[0].data.shift();
        }

        chart.update();
    }

    function animateValue(elementId, newValue) {
        const element = document.getElementById(elementId);
        if (!element) return;

        const currentValue = parseFloat(element.textContent);
        const targetValue = parseFloat(newValue);
        
        if (isNaN(currentValue) || isNaN(targetValue)) {
            element.textContent = newValue;
            return;
        }

        const duration = 1000;
        const startTime = performance.now();
        
        function update(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            const current = currentValue + (targetValue - currentValue) * progress;
            element.textContent = typeof targetValue === 'number' ? current.toFixed(1) : Math.round(current);
            
            if (progress < 1) {
                requestAnimationFrame(update);
            } else {
                element.textContent = newValue;
            }
        }
        
        requestAnimationFrame(update);
    }

    function loadHistoricalData() {
        fetch(`/lands/{{ $land->id }}/sensor-data`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    data.data.forEach(record => {
                        updateCharts(record);
                    });
                }
            })
            .catch(error => console.error('Error:', error));
    }

    function updateLatestData() {
        fetch(`/lands/{{ $land->id }}/latest-data`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateCharts(data.data);
                }
            })
            .catch(error => console.error('Error:', error));
    }

    // Listen untuk event sensor update
    channel.bind('sensor-update', function(data) {
        if (data.land_id === {{ $land->id }}) {
            updateCharts(data);
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        initCharts();
        loadHistoricalData();
        
        // Update data setiap 3 detik
        setInterval(updateLatestData, 3000);
    });
</script>
@endsection

@section('content')
<div class="page pt-16 min-h-screen pb-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">{{ $land->name }}</h1>
                    <p class="text-gray-600">{{ $land->location }}</p>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-500" id="lastUpdate">Memperbarui data...</span>
                    <a href="{{ route('monitoring') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg flex items-center transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        <span>Kembali</span>
                    </a>
                </div>
            </div>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Luas Lahan</h3>
                    <p class="text-2xl font-semibold text-gray-800">{{ $land->area }} Ha</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">ID Perangkat</h3>
                    <p class="text-2xl font-semibold text-gray-800">{{ $land->device_id }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Status</h3>
                    <p class="text-2xl font-semibold text-green-600">Aktif</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="text-sm font-medium text-gray-500">Update Terakhir</h3>
                    <p class="text-2xl font-semibold text-gray-800" id="lastUpdate">{{ $land->sensorData->first()?->created_at->diffForHumans() ?? 'Belum ada data' }}</p>
                </div>
            </div>
        </div>

        <!-- Current Values -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">pH Tanah</h3>
                        <p class="text-3xl font-semibold text-blue-600" id="currentPh">{{ $land->sensorData->first()?->ph_value ?? '0.0' }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-flask text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Kelembapan Tanah</h3>
                        <p class="text-3xl font-semibold text-green-600" id="currentMoisture">{{ $land->sensorData->first()?->moisture_value ?? '0' }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-tint text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Suhu</h3>
                        <p class="text-3xl font-semibold text-red-600" id="currentTemp">{{ $land->sensorData->first()?->temperature ?? '0' }}°C</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-thermometer-half text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500">Kelembapan Udara</h3>
                        <p class="text-3xl font-semibold text-purple-600" id="currentHumidity">{{ $land->sensorData->first()?->humidity ?? '0' }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-cloud text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">pH Tanah</h3>
                <canvas id="phChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Kelembapan Tanah</h3>
                <canvas id="moistureChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Suhu</h3>
                <canvas id="tempChart"></canvas>
            </div>
            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Kelembapan Udara</h3>
                <canvas id="humidityChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection 