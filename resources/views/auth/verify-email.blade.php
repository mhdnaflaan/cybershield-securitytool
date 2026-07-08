<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - CyberShield.lk</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .gradient-bg { background: linear-gradient(135deg, #1e3a8a 0%, #7c3aed 100%); }
        .glass-effect { background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); }
        .btn-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(124, 58, 237, 0.4); }
        .floating-shapes { position: absolute; border-radius: 50%; opacity: 0.1; pointer-events: none; }
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
            <p class="text-gray-500 mt-2 text-sm">📧 Verify Your Email</p>
        </div>

        @if (session('resent'))
            <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 rounded-lg text-green-600 text-sm">
                <i class="fas fa-check-circle mr-2"></i> A new verification link has been sent to your email.
            </div>
        @endif

        <p class="text-gray-600 text-sm mb-4">
            Thanks for signing up! Before getting started, please verify your email address by clicking the link we just emailed you.
        </p>
        <p class="text-gray-500 text-sm mb-6">
            If you didn't receive the email, we'll send you another.
        </p>

        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold py-3 rounded-xl btn-hover transition">
                <i class="fas fa-redo mr-2"></i> Resend Verification Email
            </button>
        </form>

        <div class="mt-4 text-center">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </button>
            </form>
        </div>
    </div>
</body>
</html>