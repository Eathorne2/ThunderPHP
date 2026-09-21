<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Page Not Found - <?=APP_NAME?></title>

  <style>
    :root {
      --background: #0f172a;
      --panel: #111827;
      --border: #334155;
      --text: #e5e7eb;
      --muted: #94a3b8;
      --accent: #38bdf8;
      --accent-hover: #7dd3fc;
      --danger: #ef4444;
      --radius: 24px;
      --shadow: 0 24px 70px rgba(0, 0, 0, 0.35);
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px;
      font-family: Arial, Helvetica, sans-serif;
      color: var(--text);
      background:
        radial-gradient(
          circle at top right,
          rgba(56, 189, 248, 0.14),
          transparent 32%
        ),
        radial-gradient(
          circle at bottom left,
          rgba(239, 68, 68, 0.10),
          transparent 32%
        ),
        var(--background);
    }

    .error-page {
      width: min(760px, 100%);
      padding: 56px 42px;
      text-align: center;
      background: rgba(17, 24, 39, 0.92);
      border: 1px solid var(--border);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      backdrop-filter: blur(12px);
    }

    .error-code {
      margin: 0;
      font-size: clamp(90px, 20vw, 180px);
      line-height: 0.9;
      font-weight: 900;
      letter-spacing: -10px;
      color: transparent;
      background: linear-gradient(
        135deg,
        var(--accent),
        #818cf8,
        var(--danger)
      );
      background-clip: text;
      -webkit-background-clip: text;
      user-select: none;
    }

    .divider {
      width: 70px;
      height: 5px;
      margin: 28px auto;
      border-radius: 999px;
      background: var(--accent);
    }

    h1 {
      margin: 0;
      font-size: clamp(28px, 5vw, 42px);
    }

    .message {
      max-width: 560px;
      margin: 18px auto 0;
      color: var(--muted);
      font-size: 17px;
      line-height: 1.7;
    }

    .actions {
      display: flex;
      justify-content: center;
      gap: 12px;
      flex-wrap: wrap;
      margin-top: 34px;
    }

    .button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 150px;
      padding: 13px 20px;
      border: 1px solid var(--border);
      border-radius: 12px;
      color: var(--text);
      background: rgba(255, 255, 255, 0.04);
      font-size: 14px;
      font-weight: 700;
      text-decoration: none;
      transition:
        transform 0.2s ease,
        background 0.2s ease,
        border-color 0.2s ease;
    }

    .button:hover {
      transform: translateY(-2px);
      background: rgba(255, 255, 255, 0.08);
      border-color: #475569;
    }

    .button-primary {
      color: #082f49;
      background: var(--accent);
      border-color: var(--accent);
    }

    .button-primary:hover {
      background: var(--accent-hover);
      border-color: var(--accent-hover);
    }

    .footer {
      margin-top: 38px;
      color: #64748b;
      font-size: 13px;
    }

    @media (max-width: 600px) {
      .error-page {
        padding: 42px 22px;
      }

      .error-code {
        letter-spacing: -6px;
      }

      .actions {
        flex-direction: column;
      }

      .button {
        width: 100%;
      }
    }
  </style>
</head>

<body>
  <main class="error-page">
    <div class="error-code">404</div>

    <div class="divider"></div>

    <h1>Page Not Found</h1>

    <p class="message">
      The page you are looking for may have been moved, deleted, or never
      existed. Check the address or return to the home page.
    </p>

    <div class="actions">
      <a class="button button-primary" href="<?=ROOT?>">Return Home</a>

      <a class="button" href="javascript:history.back()">
        Go Back
      </a>
    </div>

    <div class="footer">
      Error 404 &mdash; The requested page could not be found.
    </div>
  </main>
</body>
</html>