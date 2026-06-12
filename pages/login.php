<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Masuk · Five Dance School</title>
    <style>
        /* ── Reset & base ─────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --brand-dark:   #0d0a1e;
            --brand-deep:   #1a0f3c;
            --brand-purple: #7c3aed;
            --brand-light:  #a78bfa;
            --brand-faint:  #ede9fe;
            --ink:          #0f172a;
            --ink-soft:     #64748b;
            --border:       #e2e8f0;
            --surface:      #f8fafc;
            --white:        #ffffff;
            --danger-bg:    #fef2f2;
            --danger-border:#fca5a5;
            --danger-text:  #991b1b;
        }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background: var(--white); min-height: 100vh; display: flex; }
        .login-shell { display: flex; width: 100vw; min-height: 100vh; }
        /* ── Left panel ───────────────────────────────────── */
        .login-panel { flex: 1.1; position: relative; background: var(--brand-dark); overflow: hidden; display: flex; flex-direction: column; justify-content: space-between; padding: 52px 56px; color: var(--white); }
        .login-panel::before, .login-panel::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; }
        .login-panel::before { width: 420px; height: 420px; background: radial-gradient(circle, rgba(124,58,237,.35) 0%, transparent 70%); top: -100px; left: -100px; }
        .login-panel::after { width: 280px; height: 280px; background: radial-gradient(circle, rgba(167,139,250,.2) 0%, transparent 70%); bottom: 60px; right: -60px; }
        .rings { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; pointer-events: none; }
        .ring { position: absolute; border-radius: 50%; border: 1px solid rgba(167,139,250,.12); }
        .ring:nth-child(1) { width: 260px; height: 260px; animation: spin 28s linear infinite; }
        .ring:nth-child(2) { width: 400px; height: 400px; animation: spin 42s linear infinite reverse; }
        .ring:nth-child(3) { width: 540px; height: 540px; border-color: rgba(124,58,237,.08); animation: spin 60s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
        .panel-brand { position: relative; display: flex; align-items: center; gap: 12px; font-weight: 700; font-size: 1.05rem; letter-spacing: -.01em; }
        .panel-brand .logo-mark { width: 38px; height: 38px; background: rgba(255,255,255,.1); backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.15); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; color: var(--brand-light); }
        .panel-copy { position: relative; max-width: 420px; }
        .panel-copy .eyebrow { font-size: .75rem; font-weight: 600; letter-spacing: .12em; text-transform: uppercase; color: var(--brand-light); margin-bottom: 16px; }
        .panel-copy h2 { font-size: 2.6rem; font-weight: 800; line-height: 1.15; letter-spacing: -.04em; color: var(--white); margin-bottom: 18px; }
        .panel-copy h2 em { font-style: normal; color: var(--brand-light); }
        .panel-copy p { font-size: 1rem; line-height: 1.65; color: rgba(255,255,255,.55); }
        .feature-list { position: relative; display: flex; flex-direction: column; gap: 10px; margin-top: 28px; }
        .feature-item { display: flex; align-items: center; gap: 10px; font-size: .875rem; color: rgba(255,255,255,.6); }
        .feature-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--brand-light); flex-shrink: 0; }
        .panel-footer { position: relative; font-size: .8rem; color: rgba(255,255,255,.3); }
        /* ── Right panel (form) ───────────────────────────── */
        .login-form-side { flex: 1; background: var(--white); display: flex; align-items: center; justify-content: center; padding: 48px 40px; }
        .form-box { width: 100%; max-width: 360px; }
        .form-box-header { margin-bottom: 36px; }
        .form-box-header h1 { font-size: 1.65rem; font-weight: 700; color: var(--ink); letter-spacing: -.03em; margin-bottom: 6px; }
        .form-box-header p { font-size: .9rem; color: var(--ink-soft); line-height: 1.5; }
        .error-banner { display: flex; align-items: flex-start; gap: 10px; background: var(--danger-bg); border: 1px solid var(--danger-border); color: var(--danger-text); padding: 12px 14px; border-radius: 8px; font-size: .85rem; line-height: 1.5; margin-bottom: 24px; }
        .error-banner svg { flex-shrink: 0; margin-top: 1px; }
        .field-group { display: flex; flex-direction: column; gap: 20px; margin-bottom: 24px; }
        .field { display: flex; flex-direction: column; gap: 6px; }
        .field label { font-size: .82rem; font-weight: 600; color: #334155; letter-spacing: .01em; }
        .input-wrap { position: relative; display: flex; align-items: center; }
        .input-wrap .field-icon { position: absolute; left: 14px; color: #94a3b8; pointer-events: none; display: flex; }
        .input-wrap input { width: 100%; padding: 11px 14px 11px 42px; border: 1px solid var(--border); border-radius: 8px; font-size: .93rem; background: var(--surface); color: var(--ink); transition: border-color .15s, box-shadow .15s, background .15s; }
        .input-wrap input:focus { outline: none; border-color: var(--brand-purple); background: var(--white); box-shadow: 0 0 0 3px rgba(124,58,237,.12); }
        .btn-login { width: 100%; padding: 12px; background: var(--brand-purple); color: var(--white); border: none; border-radius: 8px; font-size: .95rem; font-weight: 600; cursor: pointer; transition: background .15s, transform .1s, box-shadow .15s; box-shadow: 0 4px 12px rgba(124,58,237,.3); letter-spacing: .01em; }
        .btn-login:hover  { background: #6d28d9; box-shadow: 0 6px 16px rgba(124,58,237,.35); }
        .btn-login:active { background: #5b21b6; transform: translateY(1px); box-shadow: 0 2px 8px rgba(124,58,237,.2); }
        .form-hint { margin-top: 20px; text-align: center; font-size: .8rem; color: #94a3b8; }
        @media (max-width: 840px) {
            .login-panel { display: none; }
            .login-form-side { background: #f1f5f9; }
            .form-box { background: var(--white); padding: 36px 28px; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.07); }
        }
    </style>
</head>
<body>
<div class="login-shell">
    <div class="login-panel">
        <div class="rings"><div class="ring"></div><div class="ring"></div><div class="ring"></div></div>
        <div class="panel-brand"><div class="logo-mark">5</div><span>Five Dance School</span></div>
        <div class="panel-copy">
            <p class="eyebrow">Admin Dashboard</p>
            <h2>Satu dasbor,<br>semua <em>alur kerja</em>.</h2>
            <p>Kelola murid, jadwal kelas, rekap absensi, dan slip gaji instruktur — dari satu tempat yang rapi.</p>
            <div class="feature-list">
                <div class="feature-item"><div class="feature-dot"></div>Manajemen murid &amp; status trial</div>
                <div class="feature-item"><div class="feature-dot"></div>Kalender jadwal mingguan</div>
                <div class="feature-item"><div class="feature-dot"></div>Rekap pembayaran &amp; denda otomatis</div>
                <div class="feature-item"><div class="feature-dot"></div>Slip gaji guru berbasis kehadiran</div>
                <div class="feature-item"><div class="feature-dot"></div>Rapor perkembangan murid</div>
            </div>
        </div>
        <div class="panel-footer">&copy; 2026 Five Dance School. All rights reserved.</div>
    </div>

    <div class="login-form-side">
        <div class="form-box">
            <div class="form-box-header">
                <h1>Selamat datang</h1>
                <p>Masuk dengan akun admin untuk mengakses dasbor.</p>
            </div>

            <?php if ($login_error): ?>
                <div class="error-banner">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                    <span><?= e($login_error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <input type="hidden" name="action" value="do_login">
                <div class="field-group">
                    <div class="field">
                        <label for="username">Username</label>
                        <div class="input-wrap">
                            <span class="field-icon"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg></span>
                            <input id="username" name="username" required autofocus autocomplete="username" placeholder="Masukkan username">
                        </div>
                    </div>
                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <span class="field-icon"><svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg></span>
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn-login">Masuk ke Dashboard &rarr;</button>
            </form>
            <p class="form-hint">Demo: admin / admin123</p>
        </div>
    </div>
</div>
</body>
</html>