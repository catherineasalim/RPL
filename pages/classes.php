<?php
$edit     = isset($_GET['edit']) ? class_by_id($_GET['edit']) : null;
$showForm = isset($_GET['mode']) || $edit;

// echo '<div class="page-actions"><a class="btn primary" href="?page=classes&mode=new">+ Buat Kelas</a></div>';

if ($showForm) {
    ob_start(); ?>
    <form class="form" method="post">
        <?php if (!empty($_SESSION['flash_error'])): ?>
            <div class="error-banner" style="display: flex; align-items: flex-start; gap: 10px; background: #fef2f2; border: 1px solid #fca5a5; color: #991b1b; padding: 12px 14px; border-radius: 8px; font-size: .85rem; line-height: 1.5; margin-bottom: 24px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 1px;"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <span><?= e($_SESSION['flash_error']) ?></span>
            </div>
            <?php unset($_SESSION['flash_error']); /* Hapus error setelah ditampilkan biar gak nempel terus */ ?>
        <?php endif; ?>
        
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