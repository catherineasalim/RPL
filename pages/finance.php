<?php
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