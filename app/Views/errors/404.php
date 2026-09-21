<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 &bull; Halaman Tidak Ditemukan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --surface: #f9fafb;
            --border: #e5e7eb;
            --text: #111827;
            --text-secondary: #6b7280;
            --btn-bg: #111827;
            --btn-text: #ffffff;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #09090b;
                --surface: #121215;
                --border: #27272a;
                --text: #fafafa;
                --text-secondary: #a1a1aa;
                --btn-bg: #ffffff;
                --btn-text: #09090b;
            }
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            text-align: center;
        }

        .box {
            max-width: 420px;
            width: 100%;
        }

        .code {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            margin-bottom: 0.75rem;
        }

        p {
            color: var(--text-secondary);
            font-size: 0.95rem;
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }

        .btn {
            display: inline-block;
            background: var(--btn-bg);
            color: var(--btn-text);
            text-decoration: none;
            padding: 0.65rem 1.25rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: opacity 0.15s;
        }

        .btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="code">404 Error</div>
        <h1>Halaman tidak ditemukan</h1>
        <p>Rute atau berkas yang Anda cari tidak tersedia dalam aplikasi Kyro ini.</p>
        <a href="/" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>
