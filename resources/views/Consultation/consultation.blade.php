@extends('app')

@section('scripts')
<script>
    const sampleExperts = [
            { id: 1, name: "Dr. Ani Sutrisno", speciality: "Ahli Tanaman Pangan", image: "/api/placeholder/100/100" },
            { id: 2, name: "Ir. Budi Santoso", speciality: "Spesialis Hidroponik", image: "/api/placeholder/100/100" },
            { id: 3, name: "Prof. Diana Putri", speciality: "Ahli Tanah dan Pupuk", image: "/api/placeholder/100/100" }
        ];

    function loadExperts() {
        const expertsContainer = document.getElementById('experts-container');
        expertsContainer.innerHTML = ''; // Clear existing content first
        
        sampleExperts.forEach(expert => {
            const expertCard = document.createElement('div');
            expertCard.className = 'bg-white rounded-lg overflow-hidden shadow p-4 flex items-center mb-4';
            
            expertCard.innerHTML = `
                <img src="${expert.image}" alt="${expert.name}" class="w-12 h-12 rounded-full mr-4">
                <div>
                    <h3 class="font-semibold text-gray-800">${expert.name}</h3>
                    <p class="text-sm text-gray-600">${expert.speciality}</p>
                </div>
                <button class="ml-auto bg-primary hover:bg-primary-dark text-white px-3 py-1 rounded-lg text-sm">Chat</button>
            `;
            
            expertsContainer.appendChild(expertCard);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
    loadExperts();
    });
</script>
@endsection

@section('content')
<div id="consultation" class="page pt-8 pb-16 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Konsultasi Ahli Tanaman</h1>
            <p class="text-gray-600 mb-8">Konsultasikan masalah pertanian Anda dengan ahli</p>
            
            <!-- Experts List -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Ahli Tersedia</h2>
                <div id="experts-container" class="grid grid-cols-1 gap-4">
                    <!-- Experts will be loaded here by JavaScript -->
                </div>
            </div>
            
            <!-- Chat Sample -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <div class="p-4 bg-primary text-white flex items-center">
                    <img src="/api/placeholder/100/100" alt="Expert Avatar" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <h3 class="font-semibold">Dr. Ani Sutrisno</h3>
                        <p class="text-sm text-green-100">Ahli Tanaman Pangan</p>
                    </div>
                    <div class="ml-auto flex space-x-2">
                        <button class="text-white hover:text-green-200">
                            <i class="fas fa-phone"></i>
                        </button>
                        <button class="text-white hover:text-green-200">
                            <i class="fas fa-video"></i>
                        </button>
                    </div>
                </div>
                
                <div class="p-4 h-96 overflow-y-auto bg-gray-50">
                    <div class="flex items-end mb-4">
                        <img src="/api/placeholder/100/100" alt="Expert Avatar" class="w-8 h-8 rounded-full mr-2">
                        <div class="bg-gray-200 rounded-lg rounded-bl-none p-3 max-w-xs">
                            <p class="text-gray-700">Selamat siang! Ada yang bisa saya bantu terkait tanaman Anda?</p>
                            <span class="text-xs text-gray-500 mt-1">10:30</span>
                        </div>
                    </div>
                    
                    <div class="flex items-end justify-end mb-4">
                        <div class="bg-primary text-white rounded-lg rounded-br-none p-3 max-w-xs">
                            <p>Selamat siang, Dok. Tanaman jagung saya daunnya menguning, kira-kira kenapa ya?</p>
                            <span class="text-xs text-green-100 mt-1">10:32</span>
                        </div>
                        <img src="/api/placeholder/100/100" alt="User Avatar" class="w-8 h-8 rounded-full ml-2">
                    </div>
                    
                    <div class="flex items-end mb-4">
                        <img src="/api/placeholder/100/100" alt="Expert Avatar" class="w-8 h-8 rounded-full mr-2">
                        <div class="bg-gray-200 rounded-lg rounded-bl-none p-3 max-w-xs">
                            <p class="text-gray-700">Ada beberapa kemungkinan penyebabnya. Bisa jadi kekurangan nutrisi seperti nitrogen atau ada masalah pada drainase tanah. Bisa tolong kirimkan foto tanamannya?</p>
                            <span class="text-xs text-gray-500 mt-1">10:35</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 border-t border-gray-200">
                    <form class="flex items-center">
                        <button type="button" class="text-gray-500 hover:text-primary mr-2">
                            <i class="fas fa-image"></i>
                        </button>
                        <button type="button" class="text-gray-500 hover:text-primary mr-2">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <input type="text" placeholder="Ketik pesan..." class="flex-1 border rounded-lg py-2 px-3 focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50">
                        <button type="submit" class="bg-primary text-white rounded-full w-10 h-10 flex items-center justify-center ml-2">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection