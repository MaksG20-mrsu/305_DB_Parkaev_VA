<?php
$db = new SQLite3("../data/students_database.db");
if (isset($_POST['save'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $family = $_POST['family'];
    $father_name = $_POST['father_name'];
    $sql = "UPDATE student SET name = ?, family = ?, father_name = ? WHERE id = ?";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(1, $name);
    $stmt->bindValue(2, $family);
    $stmt->bindValue(3, $father_name);
    $stmt->bindValue(4, $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
$id = $_GET['id'];
$res = $db->query("SELECT * FROM student WHERE id = $id");
$row = $res->fetchArray(SQLITE3_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Редактирование студента</title>
</head>
<style>
    form {
        text-align: center;
        padding: 8px;
        border: 2px solid black;
        width: 25%;
        font-family: Arial, serif;
        font-size: 18px;
        border-radius: 15px;
        margin: 10px auto;
    }
    input {
        height: 35px;
        border-radius: 4px;
        text-align: center;
    }
    a {
        font-weight: bold;
        font-family: Arial, serif;
    }
</style>
<body>

<form method="POST">
    <input type="hidden" name="id" value="<?= $row['id'] ?>">
    Фамилия:<br>
    <input type="text" name="family" value="<?= $row['family'] ?>"><br><br>
    Имя:<br>
    <input type="text" name="name" value="<?= $row['name'] ?>"><br><br>
    Отчество:<br>
    <input type="text" name="father_name" value="<?= $row['father_name'] ?>"><br><br>
    <input type="submit" name="save" value="Сохранить изменения">
    <br>
    <br>
    <a href="index.php">Отмена</a>
</form>
</body>
</html>