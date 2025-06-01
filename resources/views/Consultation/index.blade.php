@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Konsultasi Ahli</h1>

    {{-- Active Conversations --}}
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Percakapan Aktif</h2>

        @forelse($chattedExperts as $expert)
            <div class="border-b border-gray-200 last:border-b-0 py-4">
                <a href="{{ route('consultation.chat', $expert->id) }}" class="flex items-center justify-between hover:bg-gray-50 -mx-6 px-6 py-4 transition-colors">
                    <div class="flex items-center">
                        <img src="{{ $expert->profile_photo_path ? asset('storage/' . $expert->profile_photo_path) : asset('images/default-avatar.png') }}" alt="{{ $expert->name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                        <div>
                            <span class="text-lg font-medium text-gray-800">{{ $expert->name }}</span>
                            <p class="text-gray-600 text-sm truncate max-w-sm">{{ $expert->last_message }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-sm text-gray-500">{{ $expert->last_message_time ? $expert->last_message_time->diffForHumans() : '' }}</span>
                        <span class="block text-sm text-blue-500">Lanjutkan Chat <i class="fas fa-arrow-right ml-1"></i></span>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-gray-600">Belum ada percakapan aktif.</p>
        @endforelse
    </div>

    {{-- Start New Chat --}}
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Mulai Chat Baru</h2>

        @forelse($nonChattedExperts as $expert)
            <div class="border-b border-gray-200 last:border-b-0 py-4">
                <a href="{{ route('consultation.chat', $expert->id) }}" class="flex items-center hover:bg-gray-50 -mx-6 px-6 py-4 transition-colors">
                    <img src="{{ $expert->profile_photo_path ? asset('storage/' . $expert->profile_photo_path) : asset('images/default-avatar.png') }}" alt="{{ $expert->name }}" class="w-12 h-12 rounded-full object-cover mr-4">
                    <div>
                        <span class="text-lg font-medium text-gray-800">{{ $expert->name }}</span>
                        <p class="text-gray-600 text-sm">Ahli</p>
                    </div>
                </a>
            </div>
        @empty
            @if($chattedExperts->isEmpty())
                <p class="text-gray-600">Tidak ada ahli yang tersedia saat ini.</p>
            @else
                <p class="text-gray-600">Anda sudah memiliki percakapan aktif dengan semua ahli yang tersedia.</p>
            @endif
        @endforelse
    </div>
</div>
@endsection 