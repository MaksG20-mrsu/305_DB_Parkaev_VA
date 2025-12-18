<?php
$db = new SQLite3("../data/students_database.db");
$id = $_GET['id'];
$student_id = $_GET['student_id'];

if (isset($_POST['submit'])) {
    $stmt = $db->prepare("UPDATE exam SET id_subject=?, exam_date=?, grade=? WHERE id=?");
    $stmt->bindValue(1, $_POST['id_subject']);
    $stmt->bindValue(2, $_POST['exam_date']);
    $stmt->bindValue(3, $_POST['grade']);
    $stmt->bindValue(4, $id);
    $stmt->execute();
    header("Location: exams.php?student_id=$student_id");
    exit;
}

$exam = $db->querySingle("SELECT * FROM exam WHERE id = $id", true);
$subjects = $db->query("SELECT sl.id, sl.subject_name, sp.name as spname FROM subject_list sl JOIN speciality sp ON sl.speciality_id = sp.id");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <style>
        form { text-align: center; padding: 20px; border: 2px solid black; width: 400px; font-family: Arial, serif; font-size: 18px; border-radius: 15px; margin: 10px auto; }
        select, input { width: 90%; height: 35px; border-radius: 4px; margin-bottom: 10px; text-align: center; }
        a { font-weight: bold; font-family: Arial, serif; }
    </style>
    <title>Редактирование экзамена</title>
</head>
<body>
<form method="POST">
    <h3>Редактировать экзамен</h3>
    Дисциплина:
    <select name="id_subject">
        <?php while($sub = $subjects->fetchArray(SQLITE3_ASSOC)): ?>
            <option value="<?= $sub['id'] ?>" <?= $exam['id_subject'] == $sub['id'] ? 'selected' : '' ?>>
                <?= $sub['subject_name'] ?> (<?= $sub['spname'] ?>)
            </option>
        <?php endwhile; ?>
    </select>
    Дата: <input type="date" name="exam_date" value="<?= $exam['exam_date'] ?>">
    Оценка: <input type="number" name="grade" min="2" max="5" value="<?= $exam['grade'] ?>">
    <input type="submit" name="submit" value="Обновить">
    <br><br><a href="exams.php?student_id=<?= $student_id ?>">Назад</a>
</form>
</body>
</html>