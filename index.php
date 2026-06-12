<?php
// ============================================================
//  BOOTSTRAP
// ============================================================
session_start();
date_default_timezone_set('Asia/Jakarta');

// ============================================================
//  HELPER FUNCTIONS
// ============================================================
function money(int $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function redirect_to(string $page, string $extra = ''): never {
    header('Location: ?page=' . $page . $extra);
    exit;
}

function next_id(string $key): int {
    $_SESSION['seq'][$key] = ($_SESSION['seq'][$key] ?? 100) + 1;
    return $_SESSION['seq'][$key];
}

function capacity(string $cat): int {
    return $cat === 'Toddler' ? 10 : 20;
}

function tuition(string $cat): int {
    return ['Toddler' => 450_000, 'Kids' => 500_000, 'Teens' => 550_000][$cat] ?? 500_000;
}

function late_fee(string $date): int {
    $day = (int) date('j', strtotime($date ?: date('Y-m-d')));
    return $day >= 8 ? ($day - 7) * 50_000 : 0;
}

function student(int|string $id): ?array {
    foreach ($_SESSION['students'] as $s) {
        if ($s['id'] == $id) return $s;
    }
    return null;
}

function class_by_id(int|string $id): ?array {
    foreach ($_SESSION['classes'] as $c) {
        if ($c['id'] == $id) return $c;
    }
    return null;
}

function teacher_by_id(int|string $id): ?array {
    foreach ($_SESSION['teachers'] as $t) {
        if ($t['id'] == $id) return $t;
    }
    return null;
}

function students_in_class(int|string $classId): array {
    return array_values(
        array_filter($_SESSION['students'], fn($s) => ($s['class_id'] ?? '') == $classId)
    );
}

function active_students_in_class(int|string $classId): array {
    return array_values(
        array_filter(students_in_class($classId), fn($s) => $s['status'] === 'Aktif')
    );
}

function payroll_rate(int $count): int {
    if ($count < 3)  return 75_000;
    if ($count <= 5) return 100_000;
    if ($count <= 9) return 125_000;
    return 150_000;
}

function assessment_labels(): array {
    return [
        'technique'  => 'Teknik Gerakan',
        'rhythm'     => 'Ritme & Ketepatan Musik',
        'expression' => 'Ekspresi',
        'discipline' => 'Disiplin Latihan',
        'confidence' => 'Kepercayaan Diri',
        'teamwork'   => 'Kerja Sama',
        'progress'   => 'Perkembangan Pribadi',
    ];
}

// ============================================================
//  SEED DUMMY DATA
// ============================================================
if (!isset($_SESSION['booted'])) {
    $_SESSION['booted'] = true;
    $_SESSION['seq'] = ['students' => 6, 'classes' => 4, 'payments' => 4, 'recaps' => 3, 'expenses' => 3, 'assessments' => 2];

    $_SESSION['admin_user'] = [
        'username' => 'admin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
    ];

    $_SESSION['teachers'] = [
        ['id' => 1, 'name' => 'Ms. Clara',  'wa' => '081234567001'],
        ['id' => 2, 'name' => 'Ms. Nadine', 'wa' => '081234567002'],
        ['id' => 3, 'name' => 'Mr. Rio',    'wa' => '081234567003'],
    ];

    $_SESSION['students'] = [
        ['id' => 1, 'name' => 'Alicia Tan',   'age' => 5,  'parent' => 'Maya Tan',     'wa' => '081234567890', 'category' => 'Toddler', 'status' => 'Aktif', 'trial_date' => '2026-06-08', 'class_id' => 1, 'level' => 'Beginner'],
        ['id' => 2, 'name' => 'Ben Hartono',  'age' => 8,  'parent' => 'Rina Hartono', 'wa' => '081298765432', 'category' => 'Kids',    'status' => 'Aktif', 'trial_date' => '2026-06-09', 'class_id' => 2, 'level' => 'Level 1'],
        ['id' => 3, 'name' => 'Celine Wijaya','age' => 13, 'parent' => 'Andi Wijaya',  'wa' => '082112345678', 'category' => 'Teens',   'status' => 'Aktif', 'trial_date' => '2026-06-10', 'class_id' => 3, 'level' => 'Level 2'],
        ['id' => 4, 'name' => 'Darren Lim',   'age' => 7,  'parent' => 'Livia Lim',    'wa' => '081377788899', 'category' => 'Kids',    'status' => 'Trial', 'trial_date' => '2026-06-12', 'class_id' => 2, 'level' => 'Beginner'],
        ['id' => 5, 'name' => 'Evelyn S',     'age' => 4,  'parent' => 'Grace S',      'wa' => '081355566677', 'category' => 'Toddler', 'status' => 'Aktif', 'trial_date' => '2026-06-11', 'class_id' => 1, 'level' => 'Beginner'],
    ];

    $_SESSION['classes'] = [
        ['id' => 1, 'name' => 'Toddler A',          'day' => 'Senin',  'start' => '15:00', 'duration' => 1,   'category' => 'Toddler', 'studio' => 'Studio 1', 'teacher_ids' => [1, 2]],
        ['id' => 2, 'name' => 'Kids Basic',          'day' => 'Rabu',   'start' => '16:00', 'duration' => 1,   'category' => 'Kids',    'studio' => 'Studio 2', 'teacher_ids' => [1]],
        ['id' => 3, 'name' => 'Teens Performance',   'day' => 'Jumat',  'start' => '18:00', 'duration' => 1.5, 'category' => 'Teens',   'studio' => 'Studio 1', 'teacher_ids' => [3]],
    ];

    $_SESSION['payments'] = [
        ['id' => 1, 'student_id' => 1, 'month' => '2026-06', 'months_paid' => 1, 'discount' => 0,      'paid_date' => '2026-06-05', 'status' => 'Lunas'],
        ['id' => 2, 'student_id' => 2, 'month' => '2026-06', 'months_paid' => 2, 'discount' => 50_000, 'paid_date' => '2026-06-03', 'status' => 'Lunas'],
        ['id' => 3, 'student_id' => 3, 'month' => '2026-06', 'months_paid' => 1, 'discount' => 0,      'paid_date' => '2026-06-10', 'status' => 'Lunas'],
    ];

    $_SESSION['recaps'] = [
        ['id' => 1, 'teacher_id' => 1, 'class_id' => 1, 'date' => '2026-06-03', 'start' => '15:00', 'duration' => 1,   'transferred' => false],
        ['id' => 2, 'teacher_id' => 1, 'class_id' => 2, 'date' => '2026-06-05', 'start' => '16:00', 'duration' => 1,   'transferred' => true],
        ['id' => 3, 'teacher_id' => 3, 'class_id' => 3, 'date' => '2026-06-06', 'start' => '18:00', 'duration' => 1.5, 'transferred' => false],
    ];

    $_SESSION['expenses'] = [
        ['id' => 1, 'title' => 'Sewa Studio Juni',    'amount' => 1_500_000, 'date' => '2026-06-01'],
        ['id' => 2, 'title' => 'Konsumsi Event Trial', 'amount' => 250_000,  'date' => '2026-06-09'],
    ];

    $_SESSION['assessments'] = [
        ['id' => 1, 'student_id' => 1, 'date' => '2026-06-04', 'scores' => ['technique' => 85, 'rhythm' => 88, 'expression' => 82, 'discipline' => 90, 'confidence' => 80, 'teamwork' => 87, 'progress' => 86], 'feedback' => 'Gerakan makin rapi dan percaya diri meningkat.'],
        ['id' => 2, 'student_id' => 2, 'date' => '2026-06-04', 'scores' => ['technique' => 78, 'rhythm' => 80, 'expression' => 75, 'discipline' => 84, 'confidence' => 76, 'teamwork' => 82, 'progress' => 79], 'feedback' => 'Perlu latihan konsistensi ritme, tapi progress sudah baik.'],
    ];
}

// ============================================================
//  ROUTING & GLOBAL ACTIONS
// ============================================================
$page   = $_GET['page']   ?? 'dashboard';
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'logout') {
    unset($_SESSION['admin_logged']);
    header('Location: ./');
    exit;
}

if ($action === 'reset') {
    session_destroy();
    header('Location: ./');
    exit;
}

// ============================================================
//  LOGIN GATE
// ============================================================
$login_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'do_login') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (
        $username === $_SESSION['admin_user']['username'] &&
        password_verify($password, $_SESSION['admin_user']['password'])
    ) {
        $_SESSION['admin_logged'] = true;
        header('Location: ./');
        exit;
    }

    $login_error = 'Username atau password salah. Silakan coba lagi.';
}

if (!isset($_SESSION['admin_logged'])) {
    render_login($login_error);
    exit;
}

// ============================================================
//  CRUD & TRANSACTION PROCESSING
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_post_actions($action);
}

// GET-based export actions
if ($action === 'export_payroll') { export_payroll_csv(); }
if ($action === 'export_payments') { export_payments_csv(); }

// ============================================================
//  PAGE RENDERING
// ============================================================
match ($page) {
    'dashboard' => render_dashboard(),
    'students'  => render_students(),
    'classes'   => render_classes(),
    'payments'  => render_payments(),
    'payroll'   => render_payroll(),
    'reports'   => render_reports(),
    'finance'   => render_finance(),
    default     => redirect_to('dashboard'),
};


// ============================================================
//  ACTION HANDLER
// ============================================================
function handle_post_actions(string $action): void {
    switch ($action) {

        case 'save_student':
            $id   = $_POST['id'] ?? '';
            $data = [
                'id'         => $id ?: next_id('students'),
                'name'       => trim($_POST['name']),
                'age'        => (int) $_POST['age'],
                'parent'     => trim($_POST['parent']),
                'wa'         => trim($_POST['wa']),
                'category'   => $_POST['category'],
                'status'     => $_POST['status'],
                'trial_date' => $_POST['trial_date'],
                'class_id'   => $_POST['class_id'] ?: '',
                'level'      => $_POST['level'] ?: 'Beginner',
            ];
            if ($id) {
                foreach ($_SESSION['students'] as &$s) {
                    if ($s['id'] == $id) { $s = $data; break; }
                }
            } else {
                $_SESSION['students'][] = $data;
            }
            redirect_to('students');

        case 'delete_student':
            $_SESSION['students'] = array_values(
                array_filter($_SESSION['students'], fn($s) => $s['id'] != $_POST['id'])
            );
            redirect_to('students');

        case 'save_class':
            $id = $_POST['id'] ?? '';
            $teacher_ids = array_values(array_filter([
                $_POST['teacher1'] ?? '',
                $_POST['teacher2'] ?? '',
            ]));
            $data = [
                'id'          => $id ?: next_id('classes'),
                'name'        => trim($_POST['name']),
                'day'         => $_POST['day'],
                'start'       => $_POST['start'],
                'duration'    => (float) $_POST['duration'],
                'category'    => $_POST['category'],
                'studio'      => trim($_POST['studio']),
                'teacher_ids' => array_map('intval', $teacher_ids),
            ];
            if ($id) {
                foreach ($_SESSION['classes'] as &$c) {
                    if ($c['id'] == $id) { $c = $data; break; }
                }
            } else {
                $_SESSION['classes'][] = $data;
            }
            redirect_to('classes');

        case 'delete_class':
            $_SESSION['classes'] = array_values(
                array_filter($_SESSION['classes'], fn($c) => $c['id'] != $_POST['id'])
            );
            redirect_to('classes');

        case 'save_payment':
            $sid    = (int) $_POST['student_id'];
            $months = (int) $_POST['months_paid'];
            $_SESSION['payments'][] = [
                'id'          => next_id('payments'),
                'student_id'  => $sid,
                'month'       => $_POST['month'],
                'months_paid' => $months,
                'discount'    => $months >= 2 ? 50_000 : 0,
                'paid_date'   => $_POST['paid_date'],
                'status'      => 'Lunas',
            ];
            foreach ($_SESSION['students'] as &$st) {
                if ($st['id'] == $sid) $st['status'] = 'Aktif';
            }
            redirect_to('payments');

        case 'save_recap':
            $_SESSION['recaps'][] = [
                'id'          => next_id('recaps'),
                'teacher_id'  => (int) $_POST['teacher_id'],
                'class_id'    => (int) $_POST['class_id'],
                'date'        => $_POST['date'],
                'start'       => $_POST['start'],
                'duration'    => (float) $_POST['duration'],
                'transferred' => false,
            ];
            redirect_to('payroll');

        case 'toggle_transfer':
            foreach ($_SESSION['recaps'] as &$r) {
                if ($r['id'] == $_POST['id']) $r['transferred'] = !$r['transferred'];
            }
            redirect_to('payroll');

        case 'save_expense':
            $_SESSION['expenses'][] = [
                'id'     => next_id('expenses'),
                'title'  => trim($_POST['title']),
                'amount' => (int) $_POST['amount'],
                'date'   => $_POST['date'],
            ];
            redirect_to('finance');

        case 'save_assessment':
            $scores = [];
            foreach (array_keys(assessment_labels()) as $k) {
                $scores[$k] = (int) $_POST[$k];
            }
            $_SESSION['assessments'][] = [
                'id'         => next_id('assessments'),
                'student_id' => (int) $_POST['student_id'],
                'date'       => $_POST['date'],
                'scores'     => $scores,
                'feedback'   => trim($_POST['feedback']),
            ];
            redirect_to('reports');
    }
}

// ============================================================
//  CSV EXPORTS
// ============================================================
function export_payroll_csv(): never {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payroll-recap.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Tanggal','Guru','Kelas','Kategori','Murid Aktif','Durasi','Rate/Jam','Subtotal','Status Transfer']);
    foreach ($_SESSION['recaps'] as $r) {
        $c     = class_by_id($r['class_id']);
        $t     = teacher_by_id($r['teacher_id']);
        $count = count(active_students_in_class($r['class_id']));
        $rate  = payroll_rate($count);
        fputcsv($out, [
            $r['date'], $t['name'] ?? '-', $c['name'] ?? '-', $c['category'] ?? '-',
            $count, $r['duration'], $rate, $rate * $r['duration'],
            $r['transferred'] ? 'Sudah Transfer' : 'Belum Transfer',
        ]);
    }
    exit;
}

function export_payments_csv(): never {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="payment-recap.csv"');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Kelas','Murid','Bulan','Tanggal Bayar','Nominal','Diskon','Denda','Total']);
    foreach ($_SESSION['payments'] as $p) {
        $s    = student($p['student_id']);
        $c    = class_by_id($s['class_id'] ?? '');
        $base = tuition($s['category']) * $p['months_paid'];
        $fee  = late_fee($p['paid_date']);
        fputcsv($out, [
            $c['name'] ?? 'Belum Kelas', $s['name'] ?? '-',
            $p['month'], $p['paid_date'], $base, $p['discount'], $fee,
            $base - $p['discount'] + $fee,
        ]);
    }
    exit;
}


// ============================================================
//  LAYOUT HELPERS
// ============================================================
function layout_start(string $title): void {
    global $page;

    $nav = [
        ['dashboard', 'Overview',    '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>'],
        ['students',  'Murid',       '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'],
        ['classes',   'Kelas',       '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'],
        ['payments',  'Pembayaran',  '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>'],
        ['payroll',   'Gaji Guru',   '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>'],
        ['reports',   'Rapor',       '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'],
        ['finance',   'Keuangan',    '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'],
    ];
    ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?= e($title) ?> · Five Dance School</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .sidebar nav a svg { margin-right: 12px; display: inline-block; vertical-align: middle; }

        .calendar-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .calendar-table th,
        .calendar-table td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; vertical-align: top; }
        .calendar-table th { background-color: #f8fafc; font-weight: 600; }

        .calendar-class-item { background: #f1f5f9; padding: 6px 10px; border-radius: 6px; margin-bottom: 6px; font-size: .9em; border-left: 4px solid #6b21a8; }
        .calendar-class-item p { margin: 2px 0 0; color: #475569; font-size: .85em; }

        .sidebar .logout { margin-top: auto; padding: 12px 24px; color: #ef4444; text-decoration: none; display: flex; align-items: center; font-size: .95rem; font-weight: 500; border-top: 1px solid #f1f5f9; }
        .sidebar .logout:hover { background-color: #fef2f2; }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <div class="logo">5</div>
            <div><strong>Five Dance</strong><span>School Admin</span></div>
        </div>
        <nav>
            <?php foreach ($nav as $n): ?>
                <a class="<?= $page === $n[0] ? 'active' : '' ?>" href="?page=<?= $n[0] ?>">
                    <?= $n[2] ?><?= htmlentities($n[1]) ?>
                </a>
            <?php endforeach; ?>
        </nav>
        <div>
            <a class="logout" href="?action=logout">Sign Out</a>
            <a class="reset" style="padding:6px 24px;font-size:.8rem;opacity:.6;" href="?action=reset">Reset dummy data</a>
        </div>
    </aside>

    <main>
        <header class="topbar">
            <div>
                <p class="eyebrow">Five Dance School</p>
                <h1><?= e($title) ?></h1>
            </div>
        </header>
    <?php
}

function layout_end(): void {
    echo '</main></div></body></html>';
}

function card(string $title, string $body, string $sub = ''): void {
    echo '<section class="card">';
    echo   '<div class="card-head">';
    echo     '<div>';
    echo       '<h2>' . e($title) . '</h2>';
    if ($sub) echo '<p>' . e($sub) . '</p>';
    echo     '</div>';
    echo   '</div>';
    echo   $body;
    echo '</section>';
}


// ============================================================
//  LOGIN PAGE
// ============================================================
function render_login(string $error = ''): void {
    ?>
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

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: var(--white);
            min-height: 100vh;
            display: flex;
        }

        /* ── Two-column shell ─────────────────────────────── */
        .login-shell {
            display: flex;
            width: 100vw;
            min-height: 100vh;
        }

        /* ── Left panel ───────────────────────────────────── */
        .login-panel {
            flex: 1.1;
            position: relative;
            background: var(--brand-dark);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 52px 56px;
            color: var(--white);
        }

        /* Decorative shapes */
        .login-panel::before,
        .login-panel::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        .login-panel::before {
            width: 420px; height: 420px;
            background: radial-gradient(circle, rgba(124,58,237,.35) 0%, transparent 70%);
            top: -100px; left: -100px;
        }
        .login-panel::after {
            width: 280px; height: 280px;
            background: radial-gradient(circle, rgba(167,139,250,.2) 0%, transparent 70%);
            bottom: 60px; right: -60px;
        }

        /* Rotating rhythm rings — pure CSS, no JS */
        .rings {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
        }
        .ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(167,139,250,.12);
        }
        .ring:nth-child(1) { width: 260px; height: 260px; animation: spin 28s linear infinite; }
        .ring:nth-child(2) { width: 400px; height: 400px; animation: spin 42s linear infinite reverse; }
        .ring:nth-child(3) { width: 540px; height: 540px; border-color: rgba(124,58,237,.08); animation: spin 60s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        .panel-brand {
            position: relative;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            font-size: 1.05rem;
            letter-spacing: -.01em;
        }
        .panel-brand .logo-mark {
            width: 38px; height: 38px;
            background: rgba(255,255,255,.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 1.1rem;
            color: var(--brand-light);
        }

        .panel-copy {
            position: relative;
            max-width: 420px;
        }
        .panel-copy .eyebrow {
            font-size: .75rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--brand-light);
            margin-bottom: 16px;
        }
        .panel-copy h2 {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.15;
            letter-spacing: -.04em;
            color: var(--white);
            margin-bottom: 18px;
        }
        .panel-copy h2 em {
            font-style: normal;
            color: var(--brand-light);
        }
        .panel-copy p {
            font-size: 1rem;
            line-height: 1.65;
            color: rgba(255,255,255,.55);
        }

        /* Feature list */
        .feature-list {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 28px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .875rem;
            color: rgba(255,255,255,.6);
        }
        .feature-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--brand-light);
            flex-shrink: 0;
        }

        .panel-footer {
            position: relative;
            font-size: .8rem;
            color: rgba(255,255,255,.3);
        }

        /* ── Right panel (form) ───────────────────────────── */
        .login-form-side {
            flex: 1;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }

        .form-box {
            width: 100%;
            max-width: 360px;
        }

        .form-box-header {
            margin-bottom: 36px;
        }
        .form-box-header h1 {
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: -.03em;
            margin-bottom: 6px;
        }
        .form-box-header p {
            font-size: .9rem;
            color: var(--ink-soft);
            line-height: 1.5;
        }

        /* Error banner */
        .error-banner {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: var(--danger-bg);
            border: 1px solid var(--danger-border);
            color: var(--danger-text);
            padding: 12px 14px;
            border-radius: 8px;
            font-size: .85rem;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .error-banner svg { flex-shrink: 0; margin-top: 1px; }

        /* Form fields */
        .field-group {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 24px;
        }
        .field {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .field label {
            font-size: .82rem;
            font-weight: 600;
            color: #334155;
            letter-spacing: .01em;
        }
        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-wrap .field-icon {
            position: absolute;
            left: 14px;
            color: #94a3b8;
            pointer-events: none;
            display: flex;
        }
        .input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 42px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: .93rem;
            background: var(--surface);
            color: var(--ink);
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        .input-wrap input:focus {
            outline: none;
            border-color: var(--brand-purple);
            background: var(--white);
            box-shadow: 0 0 0 3px rgba(124,58,237,.12);
        }

        /* Submit button */
        .btn-login {
            width: 100%;
            padding: 12px;
            background: var(--brand-purple);
            color: var(--white);
            border: none;
            border-radius: 8px;
            font-size: .95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s, transform .1s, box-shadow .15s;
            box-shadow: 0 4px 12px rgba(124,58,237,.3);
            letter-spacing: .01em;
        }
        .btn-login:hover  { background: #6d28d9; box-shadow: 0 6px 16px rgba(124,58,237,.35); }
        .btn-login:active { background: #5b21b6; transform: translateY(1px); box-shadow: 0 2px 8px rgba(124,58,237,.2); }

        .form-hint {
            margin-top: 20px;
            text-align: center;
            font-size: .8rem;
            color: #94a3b8;
        }

        /* ── Mobile ───────────────────────────────────────── */
        @media (max-width: 840px) {
            .login-panel { display: none; }
            .login-form-side { background: #f1f5f9; }
            .form-box {
                background: var(--white);
                padding: 36px 28px;
                border-radius: 16px;
                box-shadow: 0 4px 24px rgba(0,0,0,.07);
            }
        }
    </style>
</head>
<body>
<div class="login-shell">

    <!-- ── Left decorative panel ───────────────────────── -->
    <div class="login-panel">
        <div class="rings">
            <div class="ring"></div>
            <div class="ring"></div>
            <div class="ring"></div>
        </div>

        <div class="panel-brand">
            <div class="logo-mark">5</div>
            <span>Five Dance School</span>
        </div>

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

        <div class="panel-footer">
            &copy; 2026 Five Dance School. All rights reserved.
        </div>
    </div>

    <!-- ── Right form panel ────────────────────────────── -->
    <div class="login-form-side">
        <div class="form-box">
            <div class="form-box-header">
                <h1>Selamat datang</h1>
                <p>Masuk dengan akun admin untuk mengakses dasbor.</p>
            </div>

            <?php if ($error): ?>
                <div class="error-banner">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="post" autocomplete="on">
                <input type="hidden" name="action" value="do_login">
                <div class="field-group">
                    <div class="field">
                        <label for="username">Username</label>
                        <div class="input-wrap">
                            <span class="field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                            </span>
                            <input id="username" name="username" required autofocus
                                   autocomplete="username" placeholder="Masukkan username">
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <span class="field-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                            </span>
                            <input id="password" type="password" name="password" required
                                   autocomplete="current-password" placeholder="••••••••">
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
    <?php
}


// ============================================================
//  PAGE: DASHBOARD
// ============================================================
function render_dashboard(): void {
    layout_start('Dashboard');

    $active  = count(array_filter($_SESSION['students'], fn($s) => $s['status'] === 'Aktif'));
    $trial   = count(array_filter($_SESSION['students'], fn($s) => $s['status'] === 'Trial'));
    $income  = 0;
    foreach ($_SESSION['payments'] as $p) {
        $s = student($p['student_id']);
        $income += tuition($s['category']) * $p['months_paid'] - $p['discount'] + late_fee($p['paid_date']);
    }
    $expense = array_sum(array_column($_SESSION['expenses'], 'amount'));

    echo '<div class="hero">
        <div>
            <p>Operational cockpit</p>
            <h2>Kelola murid, kelas, pembayaran, dan gaji dari alur yang lebih sederhana.</h2>
        </div>
        <a class="btn primary" href="?page=students&mode=new">+ Tambah Murid</a>
    </div>';

    echo '<div class="metrics">
        <div><span>Murid Aktif</span><strong>' . $active . '</strong></div>
        <div><span>Trial</span><strong>' . $trial . '</strong></div>
        <div><span>Kelas Aktif</span><strong>' . count($_SESSION['classes']) . '</strong></div>
        <div><span>Saldo Bersih</span><strong>' . money($income - $expense) . '</strong></div>
    </div>';

    echo '<div class="grid two">';

    card(
        'Prioritas Hari Ini',
        '<div class="timeline">
            <a href="?page=payments">Catat pembayaran murid</a>
            <a href="?page=payroll">Cek gaji yang belum ditransfer</a>
            <a href="?page=classes">Review kapasitas kelas</a>
        </div>',
        'Aksi utama, bukan semua fitur sekaligus.'
    );

    $rows = '';
    foreach ($_SESSION['classes'] as $c) {
        $cnt   = count(students_in_class($c['id']));
        $full  = $cnt >= capacity($c['category']);
        $badge = $full ? 'red' : 'green';
        $label = $full ? 'Full' : 'Available';
        $rows .= '<tr>
            <td><strong>' . e($c['name']) . '</strong><small>' . e($c['day'] . ' · ' . $c['start']) . '</small></td>
            <td>' . $cnt . '/' . capacity($c['category']) . '</td>
            <td><span class="badge ' . $badge . '">' . $label . '</span></td>
        </tr>';
    }

    card(
        'Ringkasan Kelas',
        '<table><tbody>' . $rows . '</tbody></table>',
        'Kapasitas dan status kelas.'
    );

    echo '</div>';
    layout_end();
}


// ============================================================
//  PAGE: STUDENTS
// ============================================================
function render_students(): void {
    layout_start('Murid');

    $edit     = isset($_GET['edit']) ? student($_GET['edit']) : null;
    $showForm = isset($_GET['mode']) || $edit;

    echo '<div class="page-actions"><a class="btn primary" href="?page=students&mode=new">+ Tambah Murid</a></div>';

    if ($showForm) {
        ob_start(); ?>
        <form class="form" method="post">
            <input type="hidden" name="action" value="save_student">
            <input type="hidden" name="id"     value="<?= e($edit['id'] ?? '') ?>">
            <div class="form-grid">
                <label>Nama Anak
                    <input required name="name" value="<?= e($edit['name'] ?? '') ?>">
                </label>
                <label>Usia
                    <input required type="number" name="age" value="<?= e($edit['age'] ?? '') ?>">
                </label>
                <label>Nama Orang Tua
                    <input required name="parent" value="<?= e($edit['parent'] ?? '') ?>">
                </label>
                <label>WhatsApp Orang Tua
                    <input required pattern="08[0-9]{8,13}" name="wa" value="<?= e($edit['wa'] ?? '') ?>">
                </label>
                <label>Kategori
                    <select name="category">
                        <option>Toddler</option>
                        <option <?= ($edit['category'] ?? '') === 'Kids'  ? 'selected' : '' ?>>Kids</option>
                        <option <?= ($edit['category'] ?? '') === 'Teens' ? 'selected' : '' ?>>Teens</option>
                    </select>
                </label>
                <label>Status
                    <select name="status">
                        <option>Trial</option>
                        <option <?= ($edit['status'] ?? '') === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
                    </select>
                </label>
                <label>Tanggal Trial
                    <input type="date" name="trial_date" value="<?= e($edit['trial_date'] ?? date('Y-m-d')) ?>">
                </label>
                <label>Level
                    <input name="level" value="<?= e($edit['level'] ?? 'Beginner') ?>">
                </label>
                <label class="wide">Masukkan ke Kelas
                    <select name="class_id">
                        <option value="">Belum masuk kelas</option>
                        <?php foreach ($_SESSION['classes'] as $c): ?>
                            <option value="<?= $c['id'] ?>"
                                <?= ($edit['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                <?= e($c['name'] . ' · ' . $c['category'] . ' · ' . $c['day'] . ' ' . $c['start']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div class="form-actions">
                <a class="btn" href="?page=students">Batal</a>
                <button class="btn primary">Simpan Murid</button>
            </div>
        </form>
        <?php
        card($edit ? 'Edit Murid' : 'Tambah Murid', ob_get_clean(), 'Murid langsung bisa dihubungkan ke jadwal kelas.');
    }

    $rows = '';
    foreach ($_SESSION['students'] as $s) {
        $c     = class_by_id($s['class_id'] ?? '');
        $badge = $s['status'] === 'Aktif' ? 'green' : 'purple';
        $rows .= '<tr>
            <td><strong>' . e($s['name']) . '</strong><small>' . e($s['parent'] . ' · ' . $s['wa']) . '</small></td>
            <td>' . e($s['category']) . '</td>
            <td>' . e($c['name'] ?? 'Belum kelas') . '</td>
            <td><span class="badge ' . $badge . '">' . e($s['status']) . '</span></td>
            <td class="right">
                <a class="btn small" href="?page=students&edit=' . $s['id'] . '">Edit</a>
                <form method="post" class="inline">
                    <input type="hidden" name="action" value="delete_student">
                    <input type="hidden" name="id"     value="' . $s['id'] . '">
                    <button class="btn small danger">Hapus</button>
                </form>
            </td>
        </tr>';
    }

    card('Daftar Murid', '<table><thead><tr>
        <th>Murid</th><th>Kategori</th><th>Kelas</th><th>Status</th><th></th>
    </tr></thead><tbody>' . $rows . '</tbody></table>');

    layout_end();
}


// ============================================================
//  PAGE: CLASSES
// ============================================================
function render_classes(): void {
    layout_start('Kelas');

    $edit     = isset($_GET['edit']) ? class_by_id($_GET['edit']) : null;
    $showForm = isset($_GET['mode']) || $edit;

    echo '<div class="page-actions"><a class="btn primary" href="?page=classes&mode=new">+ Buat Kelas</a></div>';

    if ($showForm) {
        ob_start(); ?>
        <form class="form" method="post">
            <input type="hidden" name="action" value="save_class">
            <input type="hidden" name="id"     value="<?= e($edit['id'] ?? '') ?>">
            <div class="form-grid">
                <label>Nama Kelas
                    <input required name="name" value="<?= e($edit['name'] ?? '') ?>">
                </label>
                <label>Hari
                    <select name="day">
                        <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $d): ?>
                            <option <?= ($edit['day'] ?? '') === $d ? 'selected' : '' ?>><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Jam Mulai
                    <input type="time" name="start" value="<?= e($edit['start'] ?? '15:00') ?>">
                </label>
                <label>Durasi
                    <select name="duration">
                        <option value="1">1 jam</option>
                        <option value="1.5" <?= ($edit['duration'] ?? '') == 1.5 ? 'selected' : '' ?>>1.5 jam</option>
                        <option value="2"   <?= ($edit['duration'] ?? '') == 2   ? 'selected' : '' ?>>2 jam</option>
                    </select>
                </label>
                <label>Kategori
                    <select name="category">
                        <option>Toddler</option>
                        <option <?= ($edit['category'] ?? '') === 'Kids'  ? 'selected' : '' ?>>Kids</option>
                        <option <?= ($edit['category'] ?? '') === 'Teens' ? 'selected' : '' ?>>Teens</option>
                    </select>
                </label>
                <label>Studio
                    <input name="studio" value="<?= e($edit['studio'] ?? 'Studio 1') ?>">
                </label>
                <label>Guru Utama
                    <select name="teacher1">
                        <?php foreach ($_SESSION['teachers'] as $t): ?>
                            <option value="<?= $t['id'] ?>"
                                <?= in_array($t['id'], $edit['teacher_ids'] ?? []) ? 'selected' : '' ?>>
                                <?= e($t['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>Guru Tambahan
                    <select name="teacher2">
                        <option value="">Tidak ada</option>
                        <?php foreach ($_SESSION['teachers'] as $t): ?>
                            <option value="<?= $t['id'] ?>"
                                <?= (isset($edit) && count($edit['teacher_ids']) > 1 && $edit['teacher_ids'][1] === $t['id']) ? 'selected' : '' ?>>
                                <?= e($t['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div class="form-actions">
                <a class="btn" href="?page=classes">Batal</a>
                <button class="btn primary">Simpan Kelas</button>
            </div>
        </form>
        <?php
        card($edit ? 'Edit Kelas' : 'Buat Kelas', ob_get_clean(), 'Kelas adalah pusat untuk murid, pembayaran, dan gaji.');
    }

    // Weekly calendar
    $days_order   = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $calendar_html = '<table class="calendar-table"><thead><tr>';
    foreach ($days_order as $d) {
        $calendar_html .= '<th>' . $d . '</th>';
    }
    $calendar_html .= '</tr></thead><tbody><tr>';

    foreach ($days_order as $d) {
        $calendar_html .= '<td>';
        $day_classes = array_values(array_filter($_SESSION['classes'], fn($c) => $c['day'] === $d));
        usort($day_classes, fn($a, $b) => strcmp($a['start'], $b['start']));

        foreach ($day_classes as $c) {
            $cnt          = count(students_in_class($c['id']));
            $teachers_str = implode(', ', array_map(fn($id) => teacher_by_id($id)['name'] ?? '-', $c['teacher_ids']));
            $calendar_html .= '
                <div class="calendar-class-item">
                    <strong>' . e($c['name']) . '</strong>
                    <p>' . $c['start'] . ' (' . $c['duration'] . ' jam) · ' . $c['studio'] . '</p>
                    <p>Guru: ' . e($teachers_str) . '</p>
                    <p>Murid: ' . $cnt . ' / ' . capacity($c['category']) . '</p>
                </div>';
        }

        if (empty($day_classes)) {
            $calendar_html .= '<em class="muted" style="font-size:.85em;">Tidak ada jadwal</em>';
        }
        $calendar_html .= '</td>';
    }
    $calendar_html .= '</tr></tbody></table>';
    card('Kalender Jadwal Mingguan', $calendar_html);

    // Class cards
    echo '<br><h2>Daftar Kelas (Detail Pengaturan)</h2><div class="class-grid">';
    foreach ($_SESSION['classes'] as $c) {
        $cnt          = count(students_in_class($c['id']));
        $teachers_str = implode(', ', array_map(fn($id) => teacher_by_id($id)['name'] ?? '-', $c['teacher_ids']));
        echo '
        <section class="class-card">
            <div>
                <span class="badge purple">' . e($c['category']) . '</span>
                <h2>' . e($c['name']) . '</h2>
                <p>' . e($c['day'] . ' · ' . $c['start'] . ' · ' . $c['studio']) . '</p>
                <p>Guru: ' . e($teachers_str) . '</p>
            </div>
            <div class="capacity">
                <strong>' . $cnt . '</strong>
                <span>/ ' . capacity($c['category']) . ' murid</span>
            </div>
            <div class="card-actions">
                <a class="btn small" href="?page=payments&class=' . $c['id'] . '">Lihat Murid &amp; Bayar</a>
                <a class="btn small" href="?page=classes&edit=' . $c['id'] . '">Edit</a>
            </div>
        </section>';
    }
    echo '</div>';

    layout_end();
}


// ============================================================
//  PAGE: PAYMENTS
// ============================================================
function render_payments(): void {
    layout_start('Pembayaran');

    $selected = $_GET['class'] ?? '';

    ob_start(); ?>
    <form class="form compact" method="post">
        <input type="hidden" name="action" value="save_payment">
        <label>Murid
            <select name="student_id">
                <?php foreach ($_SESSION['students'] as $s): ?>
                    <option value="<?= $s['id'] ?>"><?= e($s['name'] . ' · ' . $s['category']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Bulan
            <input type="month" name="month" value="<?= date('Y-m') ?>">
        </label>
        <label>Paket
            <select name="months_paid">
                <option value="1">1 bulan</option>
                <option value="2">2 bulan – diskon <?= money(50_000) ?></option>
            </select>
        </label>
        <label>Tanggal Bayar
            <input type="date" name="paid_date" value="<?= date('Y-m-d') ?>">
        </label>
        <button class="btn primary">Catat Lunas</button>
        <a class="btn" href="?action=export_payments">Export CSV</a>
    </form>
    <?php
    card('Catat Pembayaran', ob_get_clean(), 'Setelah lunas, status murid otomatis menjadi Aktif.');

    // Tabs
    echo '<div class="tabs">
        <a class="' . (!$selected ? 'active' : '') . '" href="?page=payments">Semua Kelas</a>';
    foreach ($_SESSION['classes'] as $c) {
        echo '<a class="' . ($selected == $c['id'] ? 'active' : '') . '" href="?page=payments&class=' . $c['id'] . '">' . e($c['name']) . '</a>';
    }
    echo '</div>';

    $classes = $selected ? [class_by_id($selected)] : $_SESSION['classes'];
    foreach ($classes as $c) {
        if (!$c) continue;
        $rows = '';
        foreach (students_in_class($c['id']) as $s) {
            $latest = null;
            foreach (array_reverse($_SESSION['payments']) as $p) {
                if ($p['student_id'] === $s['id']) { $latest = $p; break; }
            }
            $total  = $latest
                ? tuition($s['category']) * $latest['months_paid'] - $latest['discount'] + late_fee($latest['paid_date'])
                : tuition($s['category']);
            $badge  = $latest ? 'green' : 'red';
            $status = $latest ? 'Lunas' : 'Belum Bayar';
            $bayar  = $latest ? e($latest['paid_date']) : '<span class="muted">Belum ada</span>';

            $rows .= '<tr>
                <td><strong>' . e($s['name']) . '</strong><small>' . e($s['parent']) . '</small></td>
                <td>' . money(tuition($s['category'])) . '</td>
                <td>' . $bayar . '</td>
                <td>' . money($total) . '</td>
                <td><span class="badge ' . $badge . '">' . $status . '</span></td>
            </tr>';
        }
        card(
            $c['name'],
            '<table><thead><tr><th>Murid</th><th>SPP</th><th>Bayar Terakhir</th><th>Total</th><th>Status</th></tr></thead><tbody>' . $rows . '</tbody></table>',
            $c['category'] . ' · ' . $c['day'] . ' ' . $c['start']
        );
    }

    layout_end();
}


// ============================================================
//  PAGE: PAYROLL
// ============================================================
function render_payroll(): void {
    layout_start('Gaji Guru');

    ob_start(); ?>
    <form class="form compact" method="post">
        <input type="hidden" name="action" value="save_recap">
        <label>Guru
            <select name="teacher_id">
                <?php foreach ($_SESSION['teachers'] as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= e($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Kelas
            <select name="class_id">
                <?php foreach ($_SESSION['classes'] as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Tanggal
            <input type="date" name="date" value="<?= date('Y-m-d') ?>">
        </label>
        <label>Jam
            <input type="time" name="start" value="15:00">
        </label>
        <label>Durasi
            <input type="number" step="0.5" name="duration" value="1">
        </label>
        <button class="btn primary">Tambah Rekap</button>
        <a class="btn" href="?action=export_payroll">Export CSV</a>
    </form>
    <?php
    card('Input Rekap Mengajar', ob_get_clean(), 'Slip gaji dibuat dari kelas yang diajar, durasi, dan jumlah murid aktif.');

    // Group recaps by teacher
    $groups = [];
    foreach ($_SESSION['recaps'] as $r) {
        $groups[$r['teacher_id']][] = $r;
    }

    foreach ($groups as $tid => $items) {
        $t              = teacher_by_id($tid);
        $sum            = 0;
        $allTransferred = true;
        $rows           = '';

        foreach ($items as $r) {
            $c        = class_by_id($r['class_id']);
            $count    = count(active_students_in_class($r['class_id']));
            $rate     = payroll_rate($count);
            $subtotal = $rate * $r['duration'];
            $sum     += $subtotal;
            if (!$r['transferred']) $allTransferred = false;

            $rows .= '<tr>
                <td>' . e($r['date']) . '</td>
                <td><strong>' . e($c['name']) . '</strong><small>' . e($c['category']) . '</small></td>
                <td>' . $count . ' murid</td>
                <td>' . $r['duration'] . ' jam</td>
                <td>' . money($rate) . '</td>
                <td>' . money($subtotal) . '</td>
                <td>
                    <form method="post">
                        <input type="hidden" name="action" value="toggle_transfer">
                        <input type="hidden" name="id"     value="' . $r['id'] . '">
                        <button class="badge-button ' . ($r['transferred'] ? 'green' : 'red') . '">
                            ' . ($r['transferred'] ? 'Sudah transfer' : 'Belum transfer') . '
                        </button>
                    </form>
                </td>
            </tr>';
        }

        $wa_number = '62' . substr(preg_replace('/\D/', '', $t['wa']), 1);
        $body = '
            <div class="slip-head">
                <strong>Total: ' . money($sum) . '</strong>
                <span class="badge ' . ($allTransferred ? 'green' : 'red') . '">' . ($allTransferred ? 'Selesai' : 'Perlu Transfer') . '</span>
            </div>
            <table><thead><tr>
                <th>Tanggal</th><th>Kelas</th><th>Murid Aktif</th><th>Durasi</th><th>Rate</th><th>Subtotal</th><th>Status</th>
            </tr></thead><tbody>' . $rows . '</tbody></table>
            <div class="form-actions">
                <button onclick="window.print()" class="btn">Print / Save PDF</button>
                <a class="btn" href="https://wa.me/' . $wa_number . '" target="_blank">Kirim WA Guru</a>
            </div>';

        card('Slip Gaji · ' . $t['name'], $body, 'Rincian kelas yang diajar dan jumlah murid sudah terlihat.');
    }

    layout_end();
}


// ============================================================
//  PAGE: REPORTS
// ============================================================
function render_reports(): void {
    layout_start('Rapor');

    $labels = assessment_labels();
    ob_start(); ?>
    <form class="form" method="post">
        <input type="hidden" name="action" value="save_assessment">
        <div class="form-grid">
            <label>Murid
                <select name="student_id">
                    <?php foreach ($_SESSION['students'] as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Tanggal
                <input type="date" name="date" value="<?= date('Y-m-d') ?>">
            </label>
            <?php foreach ($labels as $k => $label): ?>
                <label><?= e($label) ?>
                    <input type="number" min="0" max="100" name="<?= $k ?>" value="80">
                </label>
            <?php endforeach; ?>
            <label class="wide">Feedback Guru
                <textarea name="feedback" rows="3">Progress baik, tetap latihan konsisten.</textarea>
            </label>
        </div>
        <div class="form-actions">
            <button class="btn primary">Simpan Rapor</button>
        </div>
    </form>
    <?php
    card('Input Solo Assessment', ob_get_clean());

    echo '<div class="report-grid">';
    foreach ($_SESSION['assessments'] as $a) {
        $s      = student($a['student_id']);
        $avg    = round(array_sum($a['scores']) / count($a['scores']), 1);
        $detail = '';
        foreach ($a['scores'] as $k => $v) {
            $detail .= '<div><span>' . e($labels[$k]) . '</span><strong>' . $v . '</strong></div>';
        }
        $wa_number = '62' . substr(preg_replace('/\D/', '', $s['wa']), 1);
        echo '
        <section class="report-card">
            <div class="report-top">
                <div>
                    <p>Digital Report</p>
                    <h2>' . e($s['name']) . '</h2>
                    <span>' . e($s['level']) . ' · ' . $a['date'] . '</span>
                </div>
                <strong class="score">' . $avg . '</strong>
            </div>
            <div class="score-grid">' . $detail . '</div>
            <p class="feedback">' . e($a['feedback']) . '</p>
            <div class="form-actions">
                <button onclick="window.print()" class="btn">Print / Save PDF</button>
                <a class="btn" target="_blank" href="https://wa.me/' . $wa_number . '">Kirim WA Ortu</a>
            </div>
        </section>';
    }
    echo '</div>';

    layout_end();
}


// ============================================================
//  PAGE: FINANCE
// ============================================================
function render_finance(): void {
    layout_start('Keuangan');

    ob_start(); ?>
    <form class="form compact" method="post">
        <input type="hidden" name="action" value="save_expense">
        <label>Pengeluaran
            <input name="title" required placeholder="Contoh: Sewa studio">
        </label>
        <label>Nominal
            <input type="number" name="amount" required>
        </label>
        <label>Tanggal
            <input type="date" name="date" value="<?= date('Y-m-d') ?>">
        </label>
        <button class="btn primary">Tambah</button>
    </form>
    <?php
    card('Input Pengeluaran', ob_get_clean());

    $income = 0;
    foreach ($_SESSION['payments'] as $p) {
        $s = student($p['student_id']);
        $income += tuition($s['category']) * $p['months_paid'] - $p['discount'] + late_fee($p['paid_date']);
    }
    $expense = array_sum(array_column($_SESSION['expenses'], 'amount'));

    echo '<div class="metrics">
        <div><span>Pemasukan</span><strong>'  . money($income)            . '</strong></div>
        <div><span>Pengeluaran</span><strong>' . money($expense)           . '</strong></div>
        <div><span>Saldo Bersih</span><strong>' . money($income - $expense) . '</strong></div>
    </div>';

    $rows = '';
    foreach ($_SESSION['expenses'] as $x) {
        $rows .= '<tr>
            <td><strong>' . e($x['title']) . '</strong></td>
            <td>' . e($x['date']) . '</td>
            <td>' . money($x['amount']) . '</td>
        </tr>';
    }
    card('Riwayat Pengeluaran', '<table><thead><tr><th>Item</th><th>Tanggal</th><th>Nominal</th></tr></thead><tbody>' . $rows . '</tbody></table>');

    layout_end();
}