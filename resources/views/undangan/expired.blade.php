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

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Poppins, sans-serif;
            background: radial-gradient(circle at 50% 0%, #16203a 0%, #0e1320 60%);
            color: #fff;
            text-align: center;
            padding: 24px;
        }

        .wrap {
            max-width: 26rem;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .icon {
            width: 120px;
            height: 120px;
            margin-bottom: 28px;
            animation: float 4s ease-in-out infinite;
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

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(1.6rem, 4vw, 2.4rem);
            margin: 0 0 12px;
        }

        p {
            color: rgba(255, 255, 255, .65);
            max-width: 28rem;
            line-height: 1.6;
            margin: 0 auto 28px;
            font-size: .92rem;
        }

        a.btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #0e1320;
            background: #c9a84c;
            text-decoration: none;
            font-weight: 600;
            font-size: .88rem;
            padding: .75rem 1.5rem;
            border-radius: .65rem;
            transition: transform .15s ease, box-shadow .15s ease;
        }

        a.btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(201, 168, 76, .25);
        }
    </style>
</head>

<body>
    <div class="wrap">
        <svg class="icon" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <rect x="14" y="34" width="92" height="66" rx="8" fill="#182240" stroke="#c9a84c" stroke-width="2" />
            <path d="M18 40L58 68C59.2 68.8 60.8 68.8 62 68L102 40" stroke="#c9a84c" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" />
            <circle cx="90" cy="26" r="16" fill="#0e1320" stroke="#e57373" stroke-width="2" />
            <path d="M84 20L96 32M96 20L84 32" stroke="#e57373" stroke-width="2.4" stroke-linecap="round" />
        </svg>

        <h1>Undangan tidak tersedia</h1>
        <p>Link undangan ini sudah tidak aktif (masa tayang berakhir). Data masih tersimpan di sistem, tetapi tidak
            bisa dibuka oleh tamu.</p>
        <a class="btn" href="{{ url('/') }}">
            Kembali ke Rayakan Momen
        </a>
    </div>
</body>

</html>