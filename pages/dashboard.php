<?php
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