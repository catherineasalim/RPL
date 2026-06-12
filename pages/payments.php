<?php
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