<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Password - CyberShield.lk</title>
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
        .input-focus:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.2);
            transform: translateY(-2px);
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
<body class="min-h-screen gradient-bg flex items-center justify-center p-4 relative overflow-hidden">

    <div class="floating-shapes w-64 h-64 bg-white top-10 left-10"></div>
    <div class="floating-shapes w-96 h-96 bg-white bottom-20 right-10"></div>
    <div class="floating-shapes w-48 h-48 bg-white top-1/2 left-1/2"></div>

    <div class="glass-effect rounded-3xl shadow-2xl p-8 w-full max-w-md relative z-10">
        <div class="text-center mb-6">
            <div class="inline-block bg-gradient-to-r from-blue-600 to-purple-600 p-4 rounded-2xl shadow-lg">
                <i class="fas fa-shield-alt text-4xl text-white"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mt-4">CyberShield<span class="text-purple-600">.lk</span></h1>
            <p class="text-gray-500 mt-2 text-sm">🔐 Please confirm your password</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-500 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p class="text-red-600 text-sm"><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <p class="text-sm text-gray-600 mb-4">This is a secure area. Please confirm your password before continuing.</p>

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">
                    <i class="fas fa-lock text-purple-600 mr-2"></i>Password
                </label>
                <input type="password" name="password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-xl input-focus outline-none"
                       placeholder="Enter your password">
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 rounded-xl btn-hover transition">
                <i class="fas fa-check-circle mr-2"></i> Confirm Password
            </button>
        </form>
    </div>
</body>
</html>