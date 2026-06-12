<?php
require_once __DIR__ . '/functions.php';

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
    require __DIR__ . '/pages/login.php';
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
$titles = [
    'dashboard' => 'Dashboard',
    'students'  => 'Murid',
    'classes'   => 'Kelas',
    'payments'  => 'Pembayaran',
    'payroll'   => 'Gaji Guru',
    'reports'   => 'Rapor',
    'finance'   => 'Keuangan',
];
$title = $titles[$page] ?? 'Dashboard';

// Definisikan tombol khusus untuk tiap halaman
$page_actions = [
    'students' => '<a class="btn primary" href="?page=students&mode=new">+ Tambah Murid</a>',
    'classes'  => '<a class="btn primary" href="?page=classes&mode=new">+ Buat Kelas</a>',
];
$action_html = $page_actions[$page] ?? '';

// Masukkan tombol ke dalam layout
layout_start($title, $action_html);

$page_file = __DIR__ . '/pages/' . $page . '.php';
if (file_exists($page_file)) {
    require $page_file;
} else {
    require __DIR__ . '/pages/dashboard.php';
}

layout_end();