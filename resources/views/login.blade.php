<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Dashboard Scan Records</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <!-- Header -->
            <div class="bg-white p-8 text-center border-b border-gray-200">
                <img src="{{ asset('sungho.png') }}" alt="Logo" class="h-20 mx-auto mb-4">
            </div>

            <!-- Form -->
            <div class="p-8">
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="email">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>Email
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror"
                            placeholder="admin@sungho.com"
                            required
                            autofocus
                        >
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label class="block text-gray-700 text-sm font-semibold mb-2" for="password">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>Mật khẩu
                        </label>
                        <input 
                            type="password" 
                            name="password" 
                            id="password" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror"
                            placeholder="••••••••"
                            required
                        >
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-sm text-gray-700">Ghi nhớ đăng nhập</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition duration-300 shadow-lg"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>Đăng nhập
                    </button>
                </form>

                
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>&copy; {{ date('Y') }} Sungho. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
