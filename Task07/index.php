<?php
$DB_PATH = __DIR__ . '/students_database.db';

$groups = [];
$students = [];
$selected = $_GET['group'] ?? '';

if (file_exists($DB_PATH)) {
    $pdo = new PDO('sqlite:' . $DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $groups = $pdo->query("
        SELECT DISTINCT study_group.name
        FROM study_group
        INNER JOIN student_card ON student_card.id_group = study_group.id
        WHERE CAST(strftime('%Y', student_card.end_date) AS INTEGER) <= strftime('%Y', 'now')
        ORDER BY study_group.name
    ")->fetchAll(PDO::FETCH_COLUMN);

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
        WHERE CAST(strftime('%Y', student_card.end_date) AS INTEGER) <= strftime('%Y', 'now')
    ";

    $params = [];
    if ($selected && in_array($selected, $groups)) {
        $sql .= " AND study_group.name = :group";
        $params[':group'] = $selected;
    }

    $sql .= " ORDER BY study_group.name, student.family, student.name, student.father_name";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Students</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; }
        th { background-color: #f5f5f5; }
        select, button { padding: 4px 8px; }
    </style>
</head>
<body>

<h2>Students (graduation year ≤ <?= date('Y') ?>)</h2>

<form method="get">
    Group:
    <select name="group">
        <option value="">— all groups —</option>
        <?php foreach ($groups as $g): ?>
            <option value="<?= $g ?>" <?= $g === $selected ? 'selected' : '' ?>>
                <?= $g ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit">Show</button>
</form>

<br>

<?php if (empty($students)): ?>
    <p>No students found.</p>
<?php else: ?>
    <table>
        <thead>
        <tr>
            <th>Group</th>
            <th>Speciality</th>
            <th>Full Name</th>
            <th>Sex</th>
            <th>Birth Date</th>
            <th>Card ID</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($students as $s): ?>
            <?php $fio = $s['family'] . ' ' . $s['name'] . ($s['father_name'] ? ' ' . $s['father_name'] : ''); ?>
            <?php $sex = $s['sex'] === 'male' ? 'M' : ($s['sex'] === 'female' ? 'F' : '?'); ?>
            <tr>
                <td><?= $s['group_name'] ?></td>
                <td><?= $s['speciality_name'] ?></td>
                <td><?= $fio ?></td>
                <td><?= $sex ?></td>
                <td><?= $s['birth_date'] ?></td>
                <td><?= $s['card_id'] ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

</body>
</html>