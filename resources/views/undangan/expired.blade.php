<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Undangan tidak tersedia - Rayakan Momen</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600&family=Poppins:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('logo.png') }}" type="image/x-icon">
    <style>
        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Poppins, sans-serif;
            background: #0a0e1a;
            color: #fff;
            text-align: center;
            padding: 24px;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 22% 20%, rgba(201, 168, 76, .16), transparent 45%),
                radial-gradient(circle at 78% 15%, rgba(120, 90, 200, .14), transparent 40%),
                radial-gradient(circle at 50% 100%, rgba(201, 168, 76, .1), transparent 50%);
            pointer-events: none;
        }

        body::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255, 255, 255, .035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, .035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: radial-gradient(circle at 50% 40%, #000 0%, transparent 70%);
            pointer-events: none;
        }

        .card {
            position: relative;
            z-index: 1;
            max-width: 30rem;
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3rem 2.25rem;
            border-radius: 1.5rem;
            background: linear-gradient(180deg, rgba(24, 34, 64, .55), rgba(14, 19, 32, .35));
            border: 1px solid rgba(201, 168, 76, .18);
            backdrop-filter: blur(6px);
            box-shadow: 0 30px 60px -20px rgba(0, 0, 0, .5);
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: .78rem;
            letter-spacing: .28em;
            text-transform: uppercase;
            color: #c9a84c;
            margin-bottom: 2rem;
            opacity: .85;
        }

        .icon-wrap {
            position: relative;
            width: 108px;
            height: 108px;
            margin-bottom: 30px;
            animation: float 4s ease-in-out infinite;
        }

        .icon-ring {
            position: absolute;
            inset: -14px;
            border-radius: 50%;
            border: 1px dashed rgba(201, 168, 76, .35);
            animation: spin 22s linear infinite;
        }

        .icon {
            width: 100%;
            height: 100%;
            filter: drop-shadow(0 8px 18px rgba(201, 168, 76, .18));
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-8px);
            }
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            font-size: clamp(1.5rem, 4vw, 2.1rem);
            margin: 0 0 14px;
            letter-spacing: .01em;
        }

        p {
            color: rgba(255, 255, 255, .6);
            max-width: 26rem;
            line-height: 1.7;
            margin: 0 auto 30px;
            font-size: .9rem;
        }

        a.btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #0e1320;
            background: linear-gradient(180deg, #ddbb60, #c9a84c);
            text-decoration: none;
            font-weight: 600;
            font-size: .86rem;
            padding: .8rem 1.7rem;
            border-radius: .65rem;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        a.btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(201, 168, 76, .3);
        }

        a.btn svg {
            width: 15px;
            height: 15px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="brand">Rayakan Momen</div>

        <div class="icon-wrap">
            <div class="icon-ring"></div>
            <svg class="icon" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="14" y="34" width="92" height="66" rx="8" fill="#182240" stroke="#c9a84c" stroke-width="2" />
                <path d="M18 40L58 68C59.2 68.8 60.8 68.8 62 68L102 40" stroke="#c9a84c" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" />
                <circle cx="90" cy="26" r="16" fill="#0e1320" stroke="#e57373" stroke-width="2" />
                <path d="M84 20L96 32M96 20L84 32" stroke="#e57373" stroke-width="2.4" stroke-linecap="round" />
            </svg>
        </div>

        <h1>Undangan tidak tersedia</h1>
        <p>Link undangan ini sudah tidak aktif (masa tayang berakhir). Data masih tersimpan di sistem, tetapi tidak
            bisa dibuka oleh tamu.</p>
        <a class="btn" href="{{ url('/') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"
                stroke-linejoin="round">
                <path d="M19 12H5" />
                <path d="M11 18l-6-6 6-6" />
            </svg>
            Kembali ke Website
        </a>
    </div>
</body>

</html>