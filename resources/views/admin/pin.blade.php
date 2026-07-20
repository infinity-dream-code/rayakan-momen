<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi PIN — Rayakan Momen</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .font-display { font-family: 'Playfair Display', serif; }
        #pin { letter-spacing: 0.45em; font-size: 1.35rem; text-align: center; }
    </style>
</head>
<body class="min-h-screen bg-[#0e1320] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="font-display text-3xl text-[#e8d5a3]">Rayakan Momen</h1>
            <p class="text-white/45 text-sm mt-2">Verifikasi PIN Admin</p>
        </div>

        <div class="bg-white rounded-2xl p-7 shadow-2xl">
            @if (session('error'))
                <div class="bg-red-50 text-red-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg px-4 py-3 mb-4">{{ session('success') }}</div>
            @endif

            <p class="text-sm text-gray-600 mb-4 text-center">Masukkan PIN 6 digit untuk masuk ke panel.</p>

            <form method="POST" action="{{ route('admin.pin.submit') }}" class="space-y-4" autocomplete="off">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1.5 text-gray-700 text-center">PIN</label>
                    <input id="pin" type="password" name="pin" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" minlength="6" required
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#c9a84c] focus:ring-2 focus:ring-[#c9a84c]/30"
                           placeholder="••••••" autofocus>
                    @error('pin')
                        <p class="text-red-600 text-xs mt-1 text-center">{{ $message }}</p>
                    @enderror
                </div>
                @if (isset($attemptsLeft))
                    <p class="text-xs text-center text-gray-400">Sisa percobaan: {{ $attemptsLeft }}</p>
                @endif
                <button type="submit" class="w-full py-3 rounded-xl font-semibold text-sm" style="background: linear-gradient(135deg,#dfc06a,#c9a84c); color:#12161f;">
                    <i class="fa-solid fa-shield-halved mr-2"></i> Verifikasi
                </button>
            </form>

            <form method="POST" action="{{ route('admin.logout') }}" class="mt-4 text-center">
                @csrf
                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">Batalkan &amp; logout</button>
            </form>
        </div>
    </div>
    <script>
        (function () {
            var el = document.getElementById('pin');
            if (!el) return;
            el.addEventListener('input', function () {
                el.value = el.value.replace(/\D+/g, '').slice(0, 6);
            });
        })();
    </script>
</body>
</html>
