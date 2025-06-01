@extends('app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Percakapan Konsultasi</h1>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold text-gray-700 mb-4">Daftar User yang Chat</h2>

        @forelse($conversations as $user)
            <div class="border-b border-gray-200 last:border-b-0 py-4">
                <a href="{{ route('consultation.chat', $user->id) }}" class="flex items-center justify-between hover:bg-gray-50 -mx-6 px-6 py-4 transition-colors">
                    <div class="flex items-center">
                        <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : asset('images/default-avatar.png') }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover mr-4">
                        <span class="text-lg font-medium text-gray-800">{{ $user->name }}</span>
                    </div>
                    <span class="text-sm text-gray-500">Lanjutkan Chat <i class="fas fa-arrow-right ml-2"></i></span>
                </a>
            </div>
        @empty
            <p class="text-gray-600">Belum ada percakapan konsultasi yang dimulai.</p>
        @endforelse
    </div>
</div>
@endsection 