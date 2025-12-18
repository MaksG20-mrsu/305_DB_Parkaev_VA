<?php
$db = new SQLite3('../data/students_database.db');
$groups_query = $db->query("SELECT id, name FROM study_group ORDER BY name");
if (isset($_POST['submit'])) {
    $sql = "INSERT INTO student (name, family, father_name, sex, birth_date) VALUES (?, ?, ?, ?, ?)";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(1, $_POST['name']);
    $stmt->bindValue(2, $_POST['family']);
    $stmt->bindValue(3, $_POST['father_name']);
    $stmt->bindValue(4, $_POST['sex']);
    $stmt->bindValue(5, $_POST['birth_date']);
    $stmt->execute();
    $new_id = $db->lastInsertRowID();
    $group_id = $_POST['group_id'];
    $ticket = "23" . (1000 + $new_id);
    $card_sql = "INSERT INTO student_card (id_student, id_group, ticket_number, start_date, end_date) 
                 VALUES (?, ?, ?, '2023-09-01', '2024-06-30')";
    $stmt_card = $db->prepare($card_sql);
    $stmt_card->bindValue(1, $new_id);
    $stmt_card->bindValue(2, $group_id);
    $stmt_card->bindValue(3, $ticket);
    $stmt_card->execute();

    header('Location: index.php');
    exit;
}
?>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Добавление студента</title>
    <style>
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            border: 2px solid black;
            width: 420px;
            font-family: Arial, sans-serif;
            font-size: 18px;
            border-radius: 15px;
            margin: 20px auto;
        }
        label {
            width: 100%;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 15px;
        }
        input[type="text"], input[type="date"], select, .gender-box {
            width: 200px;
            margin-left: 15px;
            height: 30px;
            border-radius: 4px;
        }
        input[type="submit"] {
            width: 150px;
            height: 35px;
            margin-top: 10px;
        }
        a { font-weight: bold; text-decoration: none; margin-top: 15px; color: blue; }
    </style>
</head>
<body>
<form action="add.php" method="post">
    <label>
        Имя: <input type="text" name="name" required>
    </label>

    <label>
        Фамилия: <input type="text" name="family" required>
    </label>

    <label>
        Отчество: <input type="text" name="father_name">
    </label>

    <label>
        Группа:
        <select name="group_id" required>
            <option value="">Выберите группу</option>
            <?php while ($group = $groups_query->fetchArray(SQLITE3_ASSOC)): ?>
                <option value="<?= $group['id'] ?>"><?= $group['name'] ?></option>
            <?php endwhile; ?>
        </select>
    </label>

    <label>
        Пол:
        <span class="gender-box">
                <input type="radio" name="sex" value="male" checked> М
                <input type="radio" name="sex" value="female"> Ж
            </span>
    </label>

    <label>
        Дата рождения: <input type="date" name="birth_date" required>
    </label>

    <input type="submit" name="submit" value="Добавить">
    <a href="index.php">На главную</a>
</form>
</body>
</html>