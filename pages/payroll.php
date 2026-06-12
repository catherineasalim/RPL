<?php
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