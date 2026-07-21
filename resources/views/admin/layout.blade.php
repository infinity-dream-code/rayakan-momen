<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin') — Rayakan Momen</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --navy: #1a2234;
            --navy-deep: #0e1320;
            --gold: #c9a84c;
            --ivory: #faf7f2;
        }
        body { font-family: 'Poppins', sans-serif; background: #f4f1eb; color: #12161f; }
        .font-display { font-family: 'Playfair Display', serif; }
        .sidebar { background: linear-gradient(180deg, #0e1320 0%, #1a2234 100%); }
        .nav-item { color: rgba(255,255,255,0.65); transition: all .2s; }
        .nav-item:hover, .nav-item.active { color: #e8d5a3; background: rgba(201,168,76,0.1); }
        .card { background: #fff; border: 1px solid rgba(201,168,76,0.2); border-radius: 1rem; }
        .btn-gold { background: linear-gradient(135deg, #dfc06a, #c9a84c); color: #12161f; font-weight: 600; }
        .btn-gold:hover { filter: brightness(1.05); }
        .stat-icon { width: 2.75rem; height: 2.75rem; border-radius: 0.75rem; display: flex; align-items: center; justify-content: center; }
        input, select, textarea { font-size: 0.875rem; }
        .form-label { display: block; font-size: 0.8125rem; font-weight: 500; margin-bottom: 0.4rem; color: #334; }
        .form-input { width: 100%; border: 1px solid #e5e0d8; border-radius: 0.65rem; padding: 0.65rem 0.85rem; background: #fff; outline: none; }
        .form-input:focus { border-color: #c9a84c; box-shadow: 0 0 0 3px rgba(201,168,76,0.15); }
        .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    </style>
</head>
<body class="min-h-screen">
    <div class="flex min-h-screen">
        <aside class="sidebar w-64 shrink-0 hidden md:flex flex-col fixed inset-y-0 left-0 z-30">
            <div class="px-6 py-6 border-b border-white/10">
                <a href="{{ route('admin.dashboard') }}" class="font-display text-xl text-[#e8d5a3]">
                    Rayakan Momen
                </a>
                <p class="text-white/40 text-xs mt-1">Panel Admin</p>
            </div>
            <nav class="flex-1 px-3 py-5 space-y-1">
                <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i> Dashboard
                </a>
                <a href="{{ route('admin.undangan.index') }}" class="nav-item {{ request()->routeIs('admin.undangan.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm">
                    <i class="fa-solid fa-envelope-open-text w-5 text-center"></i> Undangan
                </a>
                <a href="{{ route('admin.jenis.index') }}" class="nav-item {{ request()->routeIs('admin.jenis.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm">
                    <i class="fa-solid fa-layer-group w-5 text-center"></i> Jenis
                </a>
                <a href="{{ route('admin.setting.index') }}" class="nav-item {{ request()->routeIs('admin.setting.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm">
                    <i class="fa-solid fa-gear w-5 text-center"></i> Setting
                </a>
                <a href="{{ route('admin.transaksi.index') }}" class="nav-item {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm">
                    <i class="fa-solid fa-receipt w-5 text-center"></i> Transaksi
                </a>
                <a href="{{ route('admin.campaign.index') }}" class="nav-item {{ request()->routeIs('admin.campaign.*') ? 'active' : '' }} flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm">
                    <i class="fa-solid fa-bullhorn w-5 text-center"></i> Campaign
                </a>
                <a href="{{ route('landing') }}" target="_blank" class="nav-item flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm">
                    <i class="fa-solid fa-globe w-5 text-center"></i> Landing Page
                </a>
            </nav>
            <div class="px-3 py-5 border-t border-white/10">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="nav-item w-full flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm text-left">
                        <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 md:ml-64">
            <header class="bg-white/80 backdrop-blur border-b border-[#e5e0d8] sticky top-0 z-20">
                <div class="px-4 sm:px-6 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="font-display text-xl text-[#1a2234]">@yield('heading', 'Dashboard')</h1>
                        <p class="text-xs text-[#666] mt-0.5">@yield('subheading', 'Kelola undangan digitalmu')</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="hidden sm:inline text-sm text-[#666]">{{ session('demo_admin_name', 'Admin') }}</span>
                        <a href="{{ route('admin.undangan.create') }}" class="btn-gold inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm">
                            <i class="fa-solid fa-plus"></i>
                            <span class="hidden sm:inline">Tambah Undangan</span>
                        </a>
                    </div>
                </div>
            </header>

            <main class="p-4 sm:p-6 lg:p-8">
                @if (session('success'))
                    <div class="alert-success rounded-xl px-4 py-3 mb-5 text-sm">{{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div class="alert-error rounded-xl px-4 py-3 mb-5 text-sm">{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert-error rounded-xl px-4 py-3 mb-5 text-sm">
                        <ul class="list-disc pl-4 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <nav class="md:hidden fixed bottom-0 inset-x-0 bg-[#0e1320] border-t border-white/10 flex z-40">
        <a href="{{ route('admin.dashboard') }}" class="flex-1 py-3 text-center text-xs {{ request()->routeIs('admin.dashboard') ? 'text-[#e8d5a3]' : 'text-white/50' }}">
            <i class="fa-solid fa-chart-line block mb-1"></i> Dashboard
        </a>
        <a href="{{ route('admin.undangan.index') }}" class="flex-1 py-3 text-center text-xs {{ request()->routeIs('admin.undangan.*') ? 'text-[#e8d5a3]' : 'text-white/50' }}">
            <i class="fa-solid fa-envelope-open-text block mb-1"></i> Undangan
        </a>
        <a href="{{ route('admin.setting.index') }}" class="flex-1 py-3 text-center text-xs {{ request()->routeIs('admin.setting.*') ? 'text-[#e8d5a3]' : 'text-white/50' }}">
            <i class="fa-solid fa-gear block mb-1"></i> Setting
        </a>
        <a href="{{ route('admin.campaign.index') }}" class="flex-1 py-3 text-center text-xs {{ request()->routeIs('admin.campaign.*') ? 'text-[#e8d5a3]' : 'text-white/50' }}">
            <i class="fa-solid fa-bullhorn block mb-1"></i> Campaign
        </a>
        <a href="{{ route('admin.undangan.create') }}" class="flex-1 py-3 text-center text-xs text-white/50">
            <i class="fa-solid fa-plus block mb-1"></i> Tambah
        </a>
    </nav>
</body>
</html>
