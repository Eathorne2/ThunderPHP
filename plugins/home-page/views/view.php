<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>ThunderPHP — Build Powerful PHP Applications</title>

    <style>
        :root {
            --primary: #af4646;
            --primary-dark: #8e3434;
            --primary-light: #d86c6c;
            --dark: #171717;
            --dark-soft: #242424;
            --text: #292929;
            --muted: #6f6f76;
            --background: #f7f7f9;
            --surface: #ffffff;
            --border: rgba(25, 25, 25, 0.1);
            --shadow: 0 24px 70px rgba(24, 24, 27, 0.12);
            --radius: 24px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            overflow-x: hidden;
            font-family:
                Inter,
                ui-sans-serif,
                system-ui,
                -apple-system,
                BlinkMacSystemFont,
                "Segoe UI",
                sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 8% 5%, rgba(175, 70, 70, 0.13), transparent 26rem),
                radial-gradient(circle at 92% 90%, rgba(175, 70, 70, 0.1), transparent 28rem),
                var(--background);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .page-shell {
            position: relative;
            min-height: 100vh;
            isolation: isolate;
        }

        .background-grid {
            position: fixed;
            inset: 0;
            z-index: -2;
            opacity: 0.45;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(20, 20, 20, 0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(20, 20, 20, 0.035) 1px, transparent 1px);
            background-size: 42px 42px;
            mask-image: linear-gradient(to bottom, black, transparent 85%);
        }

        .background-glow {
            position: fixed;
            top: -180px;
            left: 50%;
            z-index: -1;
            width: 720px;
            height: 520px;
            border-radius: 50%;
            background: rgba(175, 70, 70, 0.12);
            filter: blur(100px);
            transform: translateX(-50%);
            pointer-events: none;
        }

        .container {
            width: min(1180px, calc(100% - 40px));
            margin-inline: auto;
        }

        .site-header {
            padding: 26px 0;
        }

        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            padding: 12px 14px 12px 20px;
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.72);
            box-shadow: 0 12px 36px rgba(24, 24, 27, 0.07);
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 1.08rem;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .brand-mark {
            position: relative;
            display: grid;
            width: 40px;
            height: 40px;
            place-items: center;
            overflow: hidden;
            border-radius: 12px;
            color: #fff;
            background: linear-gradient(145deg, var(--primary-light), var(--primary-dark));
            box-shadow: 0 10px 24px rgba(175, 70, 70, 0.3);
        }

        .brand-mark::before {
            content: "";
            width: 15px;
            height: 22px;
            background: currentColor;
            clip-path: polygon(42% 0, 100% 0, 67% 39%, 100% 39%, 24% 100%, 42% 55%, 0 55%);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .nav-link {
            padding: 10px 14px;
            border-radius: 10px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
            transition: 180ms ease;
        }

        .nav-link:hover {
            color: var(--text);
            background: rgba(25, 25, 25, 0.055);
        }

        .nav-link.primary {
            color: #fff;
            background: var(--dark);
        }

        .nav-link.primary:hover {
            background: var(--primary);
            transform: translateY(-1px);
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(360px, 0.9fr);
            align-items: center;
            gap: clamp(50px, 7vw, 100px);
            min-height: calc(100vh - 122px);
            padding: 70px 0 100px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin-bottom: 24px;
            padding: 8px 13px;
            border: 1px solid rgba(175, 70, 70, 0.2);
            border-radius: 999px;
            color: var(--primary-dark);
            background: rgba(255, 255, 255, 0.7);
            box-shadow: 0 8px 24px rgba(24, 24, 27, 0.05);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .eyebrow-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary);
            box-shadow: 0 0 0 5px rgba(175, 70, 70, 0.12);
        }

        .hero-title {
            max-width: 760px;
            margin-bottom: 24px;
            color: var(--dark);
            font-size: clamp(3.6rem, 7vw, 7.3rem);
            font-weight: 900;
            letter-spacing: -0.075em;
            line-height: 0.93;
        }

        .hero-title span {
            display: block;
            color: var(--primary);
        }

        .hero-description {
            max-width: 670px;
            margin-bottom: 34px;
            color: var(--muted);
            font-size: clamp(1rem, 1.7vw, 1.2rem);
            line-height: 1.8;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 54px;
            padding: 0 21px;
            border: 1px solid transparent;
            border-radius: 14px;
            font-size: 0.94rem;
            font-weight: 800;
            transition:
                transform 180ms ease,
                box-shadow 180ms ease,
                background 180ms ease,
                border-color 180ms ease;
        }

        .button:hover {
            transform: translateY(-3px);
        }

        .button-primary {
            color: #fff;
            background: var(--primary);
            box-shadow: 0 14px 30px rgba(175, 70, 70, 0.25);
        }

        .button-primary:hover {
            background: var(--primary-dark);
            box-shadow: 0 18px 36px rgba(175, 70, 70, 0.32);
        }

        .button-dark {
            color: #fff;
            background: var(--dark);
            box-shadow: 0 14px 30px rgba(24, 24, 27, 0.18);
        }

        .button-dark:hover {
            background: #000;
            box-shadow: 0 18px 36px rgba(24, 24, 27, 0.24);
        }

        .button-light {
            border-color: var(--border);
            color: var(--text);
            background: rgba(255, 255, 255, 0.8);
            box-shadow: 0 12px 28px rgba(24, 24, 27, 0.07);
            backdrop-filter: blur(12px);
        }

        .button-light:hover {
            border-color: rgba(175, 70, 70, 0.3);
            background: #fff;
            box-shadow: 0 18px 34px rgba(24, 24, 27, 0.1);
        }

        .button-icon {
            width: 18px;
            height: 18px;
            transition: transform 180ms ease;
        }

        .button:hover .button-icon {
            transform: translateX(3px);
        }

        .hero-note {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
            margin-top: 34px;
            color: var(--muted);
            font-size: 0.82rem;
            font-weight: 700;
        }

        .hero-note span {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .check {
            display: grid;
            width: 20px;
            height: 20px;
            place-items: center;
            border-radius: 50%;
            color: var(--primary);
            background: rgba(175, 70, 70, 0.12);
            font-size: 0.7rem;
        }

        .showcase {
            position: relative;
        }

        .showcase::before {
            content: "";
            position: absolute;
            inset: 12% -7% -8% 18%;
            z-index: -1;
            border-radius: 40px;
            background: rgba(175, 70, 70, 0.15);
            filter: blur(35px);
        }

        .code-window {
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius);
            background: var(--dark);
            box-shadow: var(--shadow);
            transform: perspective(1200px) rotateY(-4deg) rotateX(2deg);
            transition: transform 300ms ease;
        }

        .code-window:hover {
            transform: perspective(1200px) rotateY(0) rotateX(0) translateY(-5px);
        }

        .window-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 17px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.07);
            background: #202020;
        }

        .window-dots {
            display: flex;
            gap: 7px;
        }

        .window-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.17);
        }

        .window-dot:first-child {
            background: #e97070;
        }

        .window-dot:nth-child(2) {
            background: #e2b95c;
        }

        .window-dot:nth-child(3) {
            background: #65b87a;
        }

        .window-title {
            color: rgba(255, 255, 255, 0.45);
            font-family: Consolas, Monaco, monospace;
            font-size: 0.72rem;
        }

        .code-body {
            padding: 26px 24px 30px;
            color: #ddd;
            font-family: Consolas, Monaco, "Courier New", monospace;
            font-size: clamp(0.75rem, 1.2vw, 0.9rem);
            line-height: 1.85;
        }

        .code-line {
            display: grid;
            grid-template-columns: 30px 1fr;
            gap: 14px;
        }

        .line-number {
            color: rgba(255, 255, 255, 0.2);
            user-select: none;
        }

        .code-keyword {
            color: #e48d8d;
        }

        .code-function {
            color: #f1d58a;
        }

        .code-string {
            color: #8ed0a1;
        }

        .code-variable {
            color: #98c8ee;
        }

        .code-comment {
            color: #777;
        }

        .floating-card {
            position: absolute;
            right: -24px;
            bottom: -34px;
            width: min(250px, 70%);
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.9);
            box-shadow: 0 20px 50px rgba(24, 24, 27, 0.16);
            backdrop-filter: blur(18px);
            animation: float 5s ease-in-out infinite;
        }

        .floating-card-label {
            margin-bottom: 8px;
            color: var(--primary);
            font-size: 0.68rem;
            font-weight: 900;
            letter-spacing: 0.09em;
            text-transform: uppercase;
        }

        .floating-card-title {
            margin-bottom: 6px;
            color: var(--dark);
            font-size: 1.02rem;
            font-weight: 850;
        }

        .floating-card-text {
            color: var(--muted);
            font-size: 0.8rem;
            line-height: 1.6;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            padding: 0 0 70px;
        }

        .feature {
            padding: 24px;
            border: 1px solid var(--border);
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.68);
            box-shadow: 0 12px 30px rgba(24, 24, 27, 0.04);
            backdrop-filter: blur(10px);
            transition: 180ms ease;
        }

        .feature:hover {
            border-color: rgba(175, 70, 70, 0.22);
            background: #fff;
            transform: translateY(-4px);
            box-shadow: 0 18px 40px rgba(24, 24, 27, 0.08);
        }

        .feature-number {
            margin-bottom: 14px;
            color: var(--primary);
            font-family: Consolas, Monaco, monospace;
            font-size: 0.75rem;
            font-weight: 800;
        }

        .feature-title {
            margin-bottom: 9px;
            color: var(--dark);
            font-size: 1.03rem;
            font-weight: 850;
        }

        .feature-description {
            color: var(--muted);
            font-size: 0.88rem;
            line-height: 1.65;
        }

        .site-footer {
            padding: 24px 0 34px;
            color: var(--muted);
            font-size: 0.82rem;
        }

        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding-top: 24px;
            border-top: 1px solid var(--border);
        }

        .footer-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 700;
        }

        .footer-badge::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--primary);
        }

        @keyframes float {
            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 960px) {
            .hero {
                grid-template-columns: 1fr;
                min-height: auto;
                padding: 80px 0 120px;
            }

            .hero-copy {
                text-align: center;
            }

            .hero-title,
            .hero-description {
                margin-inline: auto;
            }

            .hero-actions,
            .hero-note {
                justify-content: center;
            }

            .showcase {
                width: min(650px, 100%);
                margin-inline: auto;
            }

            .code-window {
                transform: none;
            }
        }

        @media (max-width: 720px) {
            .container {
                width: min(100% - 28px, 1180px);
            }

            .site-header {
                padding-top: 14px;
            }

            .navbar {
                padding: 11px 12px 11px 15px;
            }

            .nav-links .nav-link:not(.primary) {
                display: none;
            }

            .hero {
                padding-top: 58px;
            }

            .hero-title {
                font-size: clamp(3.25rem, 16vw, 5.4rem);
            }

            .hero-actions {
                align-items: stretch;
                flex-direction: column;
                width: min(100%, 430px);
                margin-inline: auto;
            }

            .button {
                width: 100%;
            }

            .floating-card {
                right: 12px;
                bottom: -60px;
            }

            .features {
                grid-template-columns: 1fr;
                padding-top: 35px;
            }

            .footer-inner {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 460px) {
            .brand-name {
                font-size: 0.96rem;
            }

            .nav-link.primary {
                padding-inline: 11px;
                font-size: 0.78rem;
            }

            .hero-title {
                letter-spacing: -0.065em;
            }

            .code-body {
                overflow-x: auto;
                padding-inline: 16px;
            }

            .code-line {
                min-width: 450px;
            }

            .floating-card {
                position: relative;
                right: auto;
                bottom: auto;
                width: calc(100% - 28px);
                margin: -14px auto 0;
            }
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <div class="background-grid"></div>
        <div class="background-glow"></div>

        <header class="site-header">
            <div class="container">
                <nav class="navbar">
                    <a href="<?= ROOT ?>" class="brand">
                        <span class="brand-mark"></span>
                        <span class="brand-name">ThunderPHP</span>
                    </a>

                    <div class="nav-links">
                        <a href="<?= ROOT ?>/docs" class="nav-link">Documentation</a>
                        <a href="<?= ROOT ?>/thunder-ide" class="nav-link">Visual IDE</a>
                        <a href="<?= ROOT ?>/admin" class="nav-link primary">Admin Area</a>
                    </div>
                </nav>
            </div>
        </header>

        <main>
            <section class="container hero">
                <div class="hero-copy">
                    <div class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        A flexible PHP application framework
                    </div>

                    <h1 class="hero-title">
                        Build boldly.
                        <span>Move faster.</span>
                    </h1>

                    <p class="hero-description">
                        ThunderPHP gives you a clean, extensible foundation for building
                        modern PHP applications, reusable plugins and complete digital
                        ecosystems without unnecessary complexity.
                    </p>

                    <div class="hero-actions">
                        <a href="<?= ROOT ?>/docs" class="button button-primary">
                            Documentation

                            <svg class="button-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14M13 6l6 6-6 6"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </a>

                        <a href="<?= ROOT ?>/thunder-ide" class="button button-dark">
                            Thunder Visual IDE

                            <svg class="button-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="m8 9-3 3 3 3M16 9l3 3-3 3M14 5l-4 14"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </a>

                        <a href="<?= ROOT ?>/admin" class="button button-light">
                            Admin Area

                            <svg class="button-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M4 5h16v14H4V5Zm0 4h16M9 9v10"
                                      stroke="currentColor"
                                      stroke-width="2"
                                      stroke-linecap="round"
                                      stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>

                    <div class="hero-note">
                        <span>
                            <span class="check">✓</span>
                            Plugin driven
                        </span>

                        <span>
                            <span class="check">✓</span>
                            Developer friendly
                        </span>

                        <span>
                            <span class="check">✓</span>
                            Built for PHP 8+
                        </span>
                    </div>
                </div>

                <div class="showcase">
                    <div class="code-window">
                        <div class="window-toolbar">
                            <div class="window-dots">
                                <span class="window-dot"></span>
                                <span class="window-dot"></span>
                                <span class="window-dot"></span>
                            </div>

                            <span class="window-title">thunder-app.php</span>
                        </div>

                        <div class="code-body">
                            <div class="code-line">
                                <span class="line-number">1</span>
                                <span><span class="code-keyword">&lt;?php</span></span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">2</span>
                                <span></span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">3</span>
                                <span>
                                    <span class="code-comment">// Register application functionality</span>
                                </span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">4</span>
                                <span>
                                    <span class="code-function">add_action</span>(
                                    <span class="code-string">'controller'</span>,
                                    <span class="code-keyword">function</span> () {
                                </span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">5</span>
                                <span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="code-variable">$projects</span>
                                    = <span class="code-function">model</span>(
                                    <span class="code-string">'projects'</span>);
                                </span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">6</span>
                                <span></span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">7</span>
                                <span>
                                    &nbsp;&nbsp;&nbsp;&nbsp;<span class="code-function">set_value</span>(
                                    <span class="code-string">'projects'</span>,
                                    <span class="code-variable">$projects</span>);
                                </span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">8</span>
                                <span>});</span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">9</span>
                                <span></span>
                            </div>

                            <div class="code-line">
                                <span class="line-number">10</span>
                                <span>
                                    <span class="code-comment">// Extend anything. Build everything.</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="floating-card">
                        <div class="floating-card-label">ThunderPHP</div>
                        <h2 class="floating-card-title">Your framework. Your ecosystem.</h2>
                        <p class="floating-card-text">
                            Create plugins, looks, tools and full applications on one
                            flexible foundation.
                        </p>
                    </div>
                </div>
            </section>

            <section class="container features">
                <article class="feature">
                    <div class="feature-number">01 / EXTENSIBLE</div>
                    <h2 class="feature-title">Plugin-first architecture</h2>
                    <p class="feature-description">
                        Add functionality through independent plugins and connect
                        everything through actions, filters and reusable components.
                    </p>
                </article>

                <article class="feature">
                    <div class="feature-number">02 / VISUAL</div>
                    <h2 class="feature-title">Build with the Visual IDE</h2>
                    <p class="feature-description">
                        Design application structures and interfaces visually while
                        maintaining clean, editable PHP, HTML, CSS and JavaScript.
                    </p>
                </article>

                <article class="feature">
                    <div class="feature-number">03 / PRACTICAL</div>
                    <h2 class="feature-title">Made for real applications</h2>
                    <p class="feature-description">
                        Use familiar PHP conventions and practical framework tools
                        without fighting unnecessary abstraction or configuration.
                    </p>
                </article>
            </section>
        </main>

        <footer class="site-footer">
            <div class="container footer-inner">
                <span>
                    &copy; <?= date('Y') ?> ThunderPHP. Build something powerful.
                </span>

                <span class="footer-badge">
                    Powered by PHP
                </span>
            </div>
        </footer>
    </div>
</body>
</html>