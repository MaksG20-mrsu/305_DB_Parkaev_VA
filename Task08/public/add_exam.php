<?php
$db = new SQLite3("../data/students_database.db");

$students = $db->query("SELECT s.id, s.family, s.name, g.name as gname FROM student s JOIN student_card sc ON s.id = sc.id_student JOIN study_group g ON sc.id_group = g.id ORDER BY s.family");
$subjects = $db->query("SELECT sl.id, sl.subject_name, sp.name as spname, sl.course_year FROM subject_list sl JOIN speciality sp ON sl.speciality_id = sp.id ORDER BY sp.name, sl.course_year");

if (isset($_POST['submit'])) {
    $stmt = $db->prepare("INSERT INTO exam (id_student, id_subject, exam_date, grade) VALUES (?, ?, ?, ?)");
    $stmt->bindValue(1, $_POST['id_student']);
    $stmt->bindValue(2, $_POST['id_subject']);
    $stmt->bindValue(3, $_POST['exam_date']);
    $stmt->bindValue(4, $_POST['grade']);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <style>
        form { text-align: center; padding: 20px; border: 2px solid black; width: 400px; font-family: Arial, serif; font-size: 18px; border-radius: 15px; margin: 10px auto; }
        select, input { width: 90%; height: 35px; border-radius: 4px; margin-bottom: 10px; text-align: center; }
        a { font-weight: bold; font-family: Arial, serif; }
    </style>
    <title>Добавление экзамена</title>
</head>
<body>
<form method="POST">
    <h3>Новый результат экзамена</h3>
    Студент (Группа):
    <select name="id_student" required>
        <?php while($s = $students->fetchArray(SQLITE3_ASSOC)): ?>
            <option value="<?= $s['id'] ?>"><?= $s['family'] ?> <?= $s['name'] ?> (<?= $s['gname'] ?>)</option>
        <?php endwhile; ?>
    </select>
    Дисциплина:
    <select name="id_subject" required>
        <?php while($sub = $subjects->fetchArray(SQLITE3_ASSOC)): ?>
            <option value="<?= $sub['id'] ?>"><?= $sub['subject_name'] ?> [<?= $sub['spname'] ?>, <?= $sub['course_year'] ?> курс]</option>
        <?php endwhile; ?>
    </select>
    Дата: <input type="date" name="exam_date" required>
    Оценка: <input type="number" name="grade" min="2" max="5" required>
    <input type="submit" name="submit" value="Сохранить">
    <br><a href="index.php">Назад</a>
</form>
</body>
</html>