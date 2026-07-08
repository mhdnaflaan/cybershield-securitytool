<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CyberShield.lk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #1e3a8a 0%, #7c3aed 100%);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .input-focus {
            transition: all 0.3s ease;
        }
        .input-focus:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
            transform: translateY(-2px);
        }
        .btn-hover {
            transition: all 0.3s ease;
        }
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4);
        }
        .floating-shapes {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            pointer-events: none;
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 relative overflow-hidden overflow-y-auto">

    <!-- Decorative floating shapes -->
    <div class="floating-shapes w-64 h-64 bg-white top-10 left-10"></div>
    <div class="floating-shapes w-96 h-96 bg-white bottom-20 right-10"></div>
    <div class="floating-shapes w-48 h-48 bg-white top-1/2 left-1/2"></div>

    <div class="glass-effect rounded-3xl shadow-2xl p-8 w-full max-w-md relative z-10">
        <!-- Logo & Brand -->
        <div class="text-center mb-8">
            <div class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 p-4 rounded-2xl shadow-lg">
                <i class="fas fa-shield-alt text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">CyberShield</h1>
            <p class="text-gray-500 mt-2 text-sm"> Secure your digital world</p>
        </div>

        <!-- Error Messages -->
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Login Form -->
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-envelope text-purple-600 mr-2"></i>Email Address
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus outline-none"
                       placeholder="you@example.com">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-lock text-purple-600 mr-2"></i>Password
                </label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus outline-none"
                       placeholder="••••••••">
            </div>

            <div class="flex items-center justify-between mb-6">
                <label class="flex items-center text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="ml-2">Remember me</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-purple-600 hover:text-purple-800 hover:underline">
                        Forgot password?
                    </a>
                @endif
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 rounded-xl btn-hover transition">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>

        <p class="text-center text-gray-500 text-sm mt-6">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-purple-600 hover:text-purple-800 font-semibold hover:underline">
                Register now
            </a>
        </p>

        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
            <p class="text-xs text-gray-400">
                <i class="fas fa-shield-alt mr-1"></i> Protected with 256-bit encryption
            </p>
        </div>
    </div>

</body>
</html>