<?php
$edit     = isset($_GET['edit']) ? student($_GET['edit']) : null;
$showForm = isset($_GET['mode']) || $edit;

// echo '<div class="page-actions"><a class="btn primary" href="?page=students&mode=new">+ Tambah Murid</a></div>';

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