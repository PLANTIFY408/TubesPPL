@extends('app')

@section('content')
<div id="login-page" class="flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full mx-4">
        @if (session('success'))
        <div class="bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-500 rounded-lg shadow-md p-5 my-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <!-- Success check icon -->
                    <svg class="h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-green-700">Success</h3>
                    <div class="mt-2">
                        <span class="text-sm font-medium text-green-600 block sm:inline">{{ session('success') }}</span>
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if ($errors->any())
        <div class="bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-500 rounded-lg shadow-md p-5 my-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <!-- Error icon -->
                    <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-red-700">Validation Error</h3>
                    <div class="mt-2">
                        <ul class="list-disc pl-5 space-y-1 text-red-600">
                            @foreach ($errors->all() as $error)
                                <li class="text-sm font-medium">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="gradient-bg px-6 py-8 text-white text-center">
                <img src="{{ asset('images/plantify_slogan.png') }}" alt="Plantify Logo" class="mx-auto h-24">
            </div>
            
            <!-- Login Form -->
            <div id="login-form" class="px-6 py-8 {{ isset($isRegister) && $isRegister ? 'hidden' : '' }}">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Masuk ke Akun Anda</h2>
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="email">Email</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50 @error('email') border-red-500 @enderror" 
                               id="email" 
                               name="email" 
                               type="email" 
                               value="{{ old('email') }}"
                               placeholder="nama@email.com">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="password">Password</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50 @error('password') border-red-500 @enderror" 
                               id="password" 
                               name="password" 
                               type="password" 
                               placeholder="••••••••">
                    </div>
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center">
                            <input id="remember-me" 
                                   name="remember" 
                                   type="checkbox" 
                                   class="h-4 w-4 text-primary focus:ring-primary-light border-gray-300 rounded"
                                   {{ old('remember') ? 'checked' : '' }}>
                            <label for="remember-me" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                        </div>
                        <a href="#" class="text-sm text-primary hover:text-primary-dark">Lupa password?</a>
                    </div>
                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring transition-colors">
                        Masuk
                    </button>
                </form>
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Belum memiliki akun?
                        <a href="{{ route('register') }}" class="text-primary hover:text-primary-dark font-medium">Daftar sekarang</a>
                    </p>
                </div>
            </div>
            
            <!-- Register Form -->
            <div id="register-form" class="px-6 py-8 {{ isset($isRegister) && $isRegister ? '' : 'hidden' }}">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Buat Akun Baru</h2>
                <form action="{{ route('register.post') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="full-name">Nama Lengkap</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50 @error('name') border-red-500 @enderror" 
                               id="full-name" 
                               name="name" 
                               type="text" 
                               value="{{ old('name') }}"
                               placeholder="Nama Lengkap">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="register-email">Email</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50 @error('email') border-red-500 @enderror" 
                               id="register-email" 
                               name="email" 
                               type="email" 
                               value="{{ old('email') }}"
                               placeholder="nama@email.com">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="register-password">Password</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50 @error('password') border-red-500 @enderror" 
                               id="register-password" 
                               name="password" 
                               type="password" 
                               placeholder="••••••••">
                    </div>
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="confirm-password">Konfirmasi Password</label>
                        <input class="appearance-none border rounded-lg w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring focus:ring-primary-light focus:ring-opacity-50" 
                               id="confirm-password" 
                               name="password_confirmation" 
                               type="password" 
                               placeholder="••••••••">
                    </div>
                    <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-bold py-2 px-4 rounded-lg focus:outline-none focus:ring transition-colors">
                        Daftar
                    </button>
                </form>
                <div class="text-center mt-6">
                    <p class="text-sm text-gray-600">
                        Sudah memiliki akun?
                        <a href="{{ route('login') }}" class="text-primary hover:text-primary-dark font-medium">Masuk sekarang</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection