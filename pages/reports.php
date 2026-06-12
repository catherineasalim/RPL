<?php
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