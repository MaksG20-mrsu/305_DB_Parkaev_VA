<?php
$db = new SQLite3("../data/students_database.db");
$student_id = $_GET['student_id'];
$student = $db->querySingle("SELECT family, name FROM student WHERE id = $student_id", true);
$results = $db->query("SELECT e.id, sl.subject_name, e.exam_date, e.grade FROM exam e JOIN subject_list sl ON e.id_subject = sl.id WHERE e.id_student = $student_id ORDER BY e.exam_date ASC");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Результаты экзаменов</title>
    <style>
        table { border-collapse: collapse; border-width: 1px; width: 80%; margin: auto; }
        th, td { border: 1px solid black; padding: 4px; text-align: center; font-family: Arial, serif; }
        thead { background-color: lightgray; font-weight: bold; font-size: 18px; }
        tbody td { font-weight: normal; font-size: 16px; }
        a { font-weight: bold; font-family: Arial, serif; }
        h2 { font-family: Arial, serif; text-align: center; }
        .start { font-size: 14pt; padding: 8px; }
    </style>
</head>
<body>
<h2>Экзамены студента: <?= $student['family'] ?> <?= $student['name'] ?></h2>
<center>
    <a class="start" href="add_exam.php">Добавить результат</a> |
    <a class="start" href="index.php">Назад к списку</a>
</center>
<br>
<table>
    <thead>
    <tr>
        <th>Дата</th>
        <th>Предмет</th>
        <th>Оценка</th>
        <th>Действия</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $results->fetchArray(SQLITE3_ASSOC)): ?>
        <tr>
            <td><?= $row['exam_date'] ?></td>
            <td><?= $row['subject_name'] ?></td>
            <td><?= $row['grade'] ?></td>
            <td>
                <a href="edit_exam.php?id=<?= $row['id'] ?>&student_id=<?= $student_id ?>">Ред.</a> |
                <a href="delete_exam.php?id=<?= $row['id'] ?>&student_id=<?= $student_id ?>">Уд.</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>