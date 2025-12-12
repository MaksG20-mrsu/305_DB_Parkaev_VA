<?php
$DB_PATH = __DIR__ . '/students_database.db';
if (!file_exists($DB_PATH)) {
    fwrite(STDERR, "Файл базы данных '$DB_PATH' не найден.\n");
    exit(1);
}
$pdo = new PDO('sqlite:' . $DB_PATH);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$groups = $pdo->query("
    SELECT DISTINCT study_group.name
    FROM study_group
    INNER JOIN student_card ON student_card.id_group = study_group.id
    WHERE CAST(strftime('%Y', student_card.end_date) AS INTEGER) <= CAST(strftime('%Y', 'now') AS INTEGER)
    ORDER BY study_group.name
")->fetchAll(PDO::FETCH_COLUMN);
echo "Введите номер группы (1–" . count($groups) . ") или её название (точно как в списке).\n";
echo "Нажмите Enter для вывода студентов по всем группам: ";
$input = trim(fgets(STDIN));
$groupFilter = null;
if ($input !== '') {
    if (ctype_digit($input)) {
        $index = (int)$input - 1;
        if (isset($groups[$index])) {
            $groupFilter = $groups[$index];
        }
    }
    if ($groupFilter === null && in_array($input, $groups)) {
        $groupFilter = $input;
    }
    if ($groupFilter === null) {
        fwrite(STDERR, "⚠Некорректный ввод. Вывод по всем группам.\n\n");
    }
}

$sql = "
    SELECT
        study_group.name AS group_name,
        speciality.name AS speciality_name,
        student.family,
        student.name,
        student.father_name,
        student.sex,
        student.birth_date,
        student_card.id AS card_id
    FROM student
    INNER JOIN student_card ON student_card.id_student = student.id
    INNER JOIN study_group ON student_card.id_group = study_group.id
    INNER JOIN speciality ON study_group.speciality_id = speciality.id
    WHERE
        CAST(strftime('%Y', student_card.end_date) AS INTEGER) <= CAST(strftime('%Y', 'now') AS INTEGER)
";
if ($groupFilter) {
    $sql .= " AND study_group.name = " . $pdo->quote($groupFilter);
}
$sql .= "
    ORDER BY study_group.name, student.family, student.name, student.father_name;
";
$stmt = $pdo->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($students)) {
    echo "ℹСтудентов не найдено.\n";
    exit(0);
}


$headers = ['№ гр.', 'Напр. подг.', 'ФИО', 'Пол', 'Дата рожд.', '№ билета'];
$widths   = [6, 50, 35, 4, 12, 8];


$pad = function(string $text, int $width): string {
    if (mb_strlen($text) > $width) {
        $text = mb_substr($text, 0, $width - 1) . '…';
    }
    return $text . str_repeat(' ', max(0, $width - mb_strlen($text)));
};

$headerRow = [];
for ($i = 0; $i < count($headers); $i++) {
    $headerRow[] = $pad($headers[$i], $widths[$i]);
}
echo implode(' | ', $headerRow) . "\n";

// Разделитель (точно по ширинам)
$parts = [];
foreach ($widths as $w) {
    $parts[] = str_repeat('-', $w);
}
echo implode('-+-', $parts) . "\n";

// Вывод студентов
foreach ($students as $s) {
    $fio = trim($s['family'] . ' ' . $s['name'] . ($s['father_name'] ? ' ' . $s['father_name'] : ''));
    $sex = match ($s['sex']) {
        'male'   => 'м',
        'female' => 'ж',
        default  => '?',
    };

    $row = [
        $pad($s['group_name'],      $widths[0]),
        $pad($s['speciality_name'], $widths[1]),
        $pad($fio,                  $widths[2]),
        $pad($sex,                  $widths[3]),
        $pad($s['birth_date'],      $widths[4]),
        $pad($s['card_id'],         $widths[5]),
    ];

    echo implode(' | ', $row) . "\n";
}
