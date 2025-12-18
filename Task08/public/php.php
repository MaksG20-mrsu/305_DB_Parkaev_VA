<?php
$db = new SQLite3("../data/students_database.db");
$student_id = $_GET['student_id'];
$student = $db->querySingle("SELECT family, name FROM student WHERE id = $student_id", true);
$results = $db->query("SELECT * FROM exam WHERE id_student = $student_id ORDER BY exam_date ASC");
?>
<!DOCTYPE html>
<html>
<head><title>Результаты экзаменов</title></head>
<body>
<h2>Экзамены студента: <?= $student['family'] ?> <?= $student['name'] ?></h2>
<a href="add_exam.php?student_id=<?= $student_id ?>">Добавить результат</a> |
<a href="index.php">Назад к списку</a>
<br><br>
<table border="1">
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
            <td><?= $row['subject'] ?></td>
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