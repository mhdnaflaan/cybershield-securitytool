<!DOCTYPE html>
<html lang="en">
<head>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Tailwind CSS CLI Test</title>
                <!-- Link to your compiled CSS (adjust path if needed) -->
                    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
                    </head>
                    <body class="bg-gray-100">
    <div class="max-w-md mx-auto mt-20 p-6 bg-white rounded-xl shadow-md space-y-4">
            <h1 class="text-3xl font-bold text-center text-blue-600">
                        Tailwind CSS is working! 🎉
                                </h1>
                                <div class=" bg-red-700">
                                     <p class="text-gray-600 text-center mt-20">
                                                    If you see this styled box, the Tailwind CLI successfully compiled your CSS.
                                                            </p>
                                </div>
                                       
                                                                    <div class="flex justify-center">
                                                                                <button class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition">
                                                                                                Click me
                                                                                                            </button>
                                                                                                                    </div>
                                                                                                                        </div>
</body>
</html>