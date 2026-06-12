<?php
// ============================================================
//  BOOTSTRAP & HELPERS
// ============================================================
session_start();
date_default_timezone_set('Asia/Jakarta');

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
            $category = $_POST['category'];
            
            // Filter input guru, hapus yang kosong, dan cegah nama guru yang sama di-input 2 kali
            $teacher_ids = array_values(array_unique(array_filter([
                $_POST['teacher1'] ?? '',
                $_POST['teacher2'] ?? '',
            ])));

            // ⛔ RULE 1: Hanya kelas Toddler yang boleh punya > 1 guru
            if ($category !== 'Toddler' && count($teacher_ids) > 1) {
                $_SESSION['flash_error'] = "Gagal disimpan: Hanya kelas kategori Toddler yang boleh memiliki 2 guru.";
                redirect_to('classes', $id ? "&edit=$id" : "&mode=new");
            }

            // Siapkan data waktu kelas baru untuk dicek
            $day      = $_POST['day'];
            $start    = $_POST['start'];
            $duration = (float) $_POST['duration'];
            
            // Konversi jam mulai ke total menit (misal 15:30 -> 15*60 + 30 = 930) agar gampang dihitung
            [$h, $m] = explode(':', $start);
            $new_start_min = (int)$h * 60 + (int)$m;
            $new_end_min   = $new_start_min + (int)($duration * 60);

            // ⛔ RULE 2: Cek bentrok / clash jadwal guru
            foreach ($teacher_ids as $tid) {
                foreach ($_SESSION['classes'] as $c) {
                    if ($c['id'] == $id) continue; // Jangan cek dengan dirinya sendiri kalau lagi mode Edit
                    if ($c['day'] !== $day) continue; // Cuma peduli kalau harinya sama
                    if (!in_array($tid, $c['teacher_ids'])) continue; // Cuma peduli kalau guru ini ngajar di kelas tsb

                    // Hitung durasi kelas yang sudah ada di database
                    [$ch, $cm] = explode(':', $c['start']);
                    $c_start_min = (int)$ch * 60 + (int)$cm;
                    $c_end_min   = $c_start_min + (int)($c['duration'] * 60);

                    // Rumus Overlap: Jadwal A mulai sebelum B selesai, DAN A selesai setelah B mulai
                    if ($new_start_min < $c_end_min && $c_start_min < $new_end_min) {
                        $t_info = teacher_by_id($tid);
                        $_SESSION['flash_error'] = "Jadwal bentrok! " . e($t_info['name'] ?? 'Guru') . " sudah ada jadwal di kelas " . e($c['name']) . " pada hari $day jam " . $c['start'] . ".";
                        redirect_to('classes', $id ? "&edit=$id" : "&mode=new");
                    }
                }
            }

            // ✅ Lolos semua validasi, mari kita simpan!
            $data = [
                'id'          => $id ?: next_id('classes'),
                'name'        => trim($_POST['name']),
                'day'         => $day,
                'start'       => $start,
                'duration'    => $duration,
                'category'    => $category,
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
function layout_start(string $title, string $action_html = ''): void {
    global $page;

    $nav = [
        ['dashboard', 'Overview',   '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>'],
        ['students',  'Murid',      '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>'],
        ['classes',   'Kelas',      '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>'],
        ['payments',  'Pembayaran', '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>'],
        ['payroll',   'Gaji Guru',  '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>'],
        ['reports',   'Rapor',      '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>'],
        ['finance',   'Keuangan',   '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>'],
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
        <header class="topbar" style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <p class="eyebrow">Five Dance School</p>
                <h1><?= e($title) ?></h1>
            </div>
            
            <?php if ($action_html): ?>
                <div><?= $action_html ?></div>
            <?php endif; ?>
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