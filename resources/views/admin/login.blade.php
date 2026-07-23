<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin — Rayakan Momen</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="shortcut icon" href="{{ asset('images/logo.png') }}" type="image/x-icon">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .font-display {
            font-family: 'Playfair Display', serif;
        }
    </style>
</head>

<body class="min-h-screen bg-[#0e1320] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="font-display text-3xl text-[#e8d5a3]">Rayakan Momen</h1>
            <p class="text-white/45 text-sm mt-2">Login Panel Admin</p>
        </div>

        <div class="bg-white rounded-2xl p-7 shadow-2xl">
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.submit') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-gray-700">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#c9a84c] focus:ring-2 focus:ring-[#c9a84c]/30"
                        placeholder="admin@gmail.com">
                    @error('email')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-gray-700">Password</label>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#c9a84c] focus:ring-2 focus:ring-[#c9a84c]/30"
                        placeholder="••••••••">
                    @error('password')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full py-3 rounded-xl font-semibold text-sm"
                    style="background: linear-gradient(135deg,#dfc06a,#c9a84c); color:#12161f;">
                    <i class="fa-solid fa-right-to-bracket mr-2"></i> Masuk
                </button>
            </form>
        </div>

        <p class="text-center text-white/30 text-xs mt-6">
            <a href="{{ route('landing') }}" class="hover:text-[#e8d5a3]">← Kembali ke landing page</a>
        </p>
    </div>
</body>

</html>
