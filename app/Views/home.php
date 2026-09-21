<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title) ?> &bull; <?= e($version) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Geist:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --surface: #f9fafb;
            --surface-subtle: #f3f4f6;
            --border: #e5e7eb;
            --border-hover: #d1d5db;
            --text: #111827;
            --text-secondary: #4b5563;
            --text-muted: #9ca3af;
            --accent: #000000;
            --accent-text: #ffffff;
            --code-bg: #f3f4f6;
            --badge-bg: #f3f4f6;
            --badge-text: #374151;
            --success-dot: #10b981;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #09090b;
                --surface: #121215;
                --surface-subtle: #18181b;
                --border: #27272a;
                --border-hover: #3f3f46;
                --text: #fafafa;
                --text-secondary: #a1a1aa;
                --text-muted: #71717a;
                --accent: #ffffff;
                --accent-text: #09090b;
                --code-bg: #18181b;
                --badge-bg: #18181b;
                --badge-text: #d4d4d8;
                --success-dot: #10b981;
            }
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Geist', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
        }

        .wrapper {
            max-width: 680px;
            width: 100%;
        }

        /* Top Brand & Badge */
        .header {
            margin-bottom: 2.25rem;
        }

        .brand-line {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            font-size: 1.25rem;
            letter-spacing: -0.03em;
            color: var(--text);
            text-decoration: none;
        }

        .brand-symbol {
            width: 28px;
            height: 28px;
            background: var(--text);
            color: var(--bg);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .version-badge {
            font-size: 0.75rem;
            font-weight: 500;
            background: var(--badge-bg);
            color: var(--badge-text);
            border: 1px solid var(--border);
            padding: 0.25rem 0.65rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .status-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--success-dot);
        }

        /* Hero Text */
        h1 {
            font-size: clamp(2rem, 4vw, 2.75rem);
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.035em;
            margin-bottom: 0.75rem;
            color: var(--text);
        }

        .lead {
            font-size: 1.05rem;
            color: var(--text-secondary);
            font-weight: 400;
            line-height: 1.6;
            margin-bottom: 1.75rem;
        }

        /* Quick Command Box */
        .terminal-box {
            background: var(--code-bg);
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-family: 'JetBrains Mono', ui-monospace, monospace;
            font-size: 0.875rem;
            margin-bottom: 2rem;
        }

        .terminal-command {
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .terminal-prompt {
            color: var(--text-muted);
            user-select: none;
        }

        .copy-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.75rem;
            font-family: inherit;
            cursor: pointer;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            transition: all 0.15s ease;
        }

        .copy-btn:hover {
            color: var(--text);
            background: var(--surface);
        }

        /* Status Details Row */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
            margin-bottom: 2rem;
        }

        .info-card {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.85rem;
            background: var(--surface);
            transition: border-color 0.15s ease;
        }

        .info-card:hover {
            border-color: var(--border-hover);
        }

        .info-label {
            font-size: 0.725rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .info-value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text);
        }

        /* Quick Action Links */
        .links-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .link-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.85rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            text-decoration: none;
            color: var(--text);
            background: var(--bg);
            transition: all 0.15s ease;
        }

        .link-item:hover {
            background: var(--surface);
            border-color: var(--border-hover);
            transform: translateY(-1px);
        }

        .link-left {
            display: flex;
            flex-direction: column;
        }

        .link-title {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .link-desc {
            font-size: 0.775rem;
            color: var(--text-muted);
            margin-top: 0.1rem;
        }

        .link-arrow {
            color: var(--text-muted);
            font-size: 0.9rem;
            transition: transform 0.15s ease;
        }

        .link-item:hover .link-arrow {
            color: var(--text);
            transform: translateX(2px);
        }

        /* Footer */
        footer {
            margin-top: 2.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        footer a {
            color: var(--text-secondary);
            text-decoration: none;
        }

        footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 540px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
            footer {
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Header & Branding -->
        <div class="header">
            <div class="brand-line">
                <a href="/" class="brand">
                    <div class="brand-symbol">K</div>
                    <span>Kyro</span>
                </a>
                <div class="version-badge">
                    <span class="status-dot"></span>
                    <span><?= e($version) ?> &bull; PHP <?= e($php_version) ?></span>
                </div>
            </div>

            <h1>Framework PHP sederhana, cepat, dan terstruktur.</h1>
            <p class="lead">
                Kyro menyediakan fondasi MVC yang ringan dengan Active Record ORM, routing dinamis, middleware, dan CLI generator siap pakai.
            </p>

            <!-- Copyable CLI Command -->
            <div class="terminal-box">
                <div class="terminal-command">
                    <span class="terminal-prompt">$</span>
                    <span id="cmd-text">php kyro serve</span>
                </div>
                <button class="copy-btn" onclick="copyCommand(this)">Salin</button>
            </div>
        </div>

        <!-- Clean Diagnostics Cards -->
        <div class="info-grid">
            <div class="info-card">
                <div class="info-label">Environment</div>
                <div class="info-value" style="text-transform: capitalize;"><?= e($app_env) ?> (Debug: <?= $app_debug ? 'On' : 'Off' ?>)</div>
            </div>
            <div class="info-card">
                <div class="info-label">Database</div>
                <div class="info-value">
                    <?= $db_status ? 'Terhubung' : 'Standby (' . e(config('database.default', 'mysql')) . ')' ?>
                </div>
            </div>
            <div class="info-card">
                <div class="info-label">Routes</div>
                <div class="info-value"><?= e((string)$routes_count) ?> Rute Aktif</div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="links-list">
            <a href="/api/status" class="link-item" target="_blank">
                <div class="link-left">
                    <span class="link-title">Cek Endpoint API</span>
                    <span class="link-desc">Menguji routing JSON otomatis di /api/status</span>
                </div>
                <span class="link-arrow">&rarr;</span>
            </a>

            <a href="/hello/Developer" class="link-item" target="_blank">
                <div class="link-left">
                    <span class="link-title">Uji Parameter Dinamis</span>
                    <span class="link-desc">Mencoba ekstraksi parameter URL di /hello/{name}</span>
                </div>
                <span class="link-arrow">&rarr;</span>
            </a>
        </div>

        <!-- Footer -->
        <footer>
            <span>Kyro Framework &bull; Siap untuk proyek Anda.</span>
            <span>Zona Waktu: <?= e($timezone) ?></span>
        </footer>
    </div>

    <script>
        function copyCommand(btn) {
            const text = document.getElementById('cmd-text').innerText;
            navigator.clipboard.writeText(text).then(() => {
                const prev = btn.innerText;
                btn.innerText = 'Tersalin';
                setTimeout(() => {
                    btn.innerText = prev;
                }, 1500);
            });
        }
    </script>
</body>
</html>