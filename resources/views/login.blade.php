<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CyberShield</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gradient-to-r from-blue-700 to-purple-700 flex items-center justify-center min-h-screen">

    <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-6">
            <i class="fas fa-shield-alt text-5xl text-blue-600"></i>
            <h1 class="text-3xl font-bold text-gray-800 mt-2">CyberShield</h1>
            <p class="text-gray-500 mt-2">Login to access your security toolkit</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border-l-4 border-red-500 text-red-700 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-2">Email Address</label>
                <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="user@example.com" required>
            </div>
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:outline-none" placeholder="••••••" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl transition">Login</button>
        </form>

        <div class="text-center mt-6 text-sm text-gray-500">
            Demo credentials: <span class="font-mono">user@example.com</span> / <span class="font-mono">password</span>
        </div>
    </div>

</body>
</html>