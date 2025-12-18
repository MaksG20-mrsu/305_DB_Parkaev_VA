<?php
$db = new SQLite3("../data/students_database.db");
$groups_result = $db->query("SELECT name FROM study_group ORDER BY name");
$all_groups = [];
while ($g = $groups_result->fetchArray(SQLITE3_ASSOC)) {
    $all_groups[] = $g['name'];
}
$selected_groups = isset($_GET['filter_groups']) ? $_GET['filter_groups'] : [];
$sql = "SELECT DISTINCT student.id AS id, study_group.name AS group_name, student.family, student.name, student.father_name
FROM student
INNER JOIN student_card ON student.id = student_card.id_student
INNER JOIN study_group ON student_card.id_group = study_group.id";
if (!empty($selected_groups)) {
    $group_list = "'" . implode("','", $selected_groups) . "'";
    $sql .= " WHERE study_group.name IN ($group_list)";
}
$sql .= " ORDER BY study_group.name, student.family";
$result = $db->query($sql);
?>

<!DOCTYPE html>
<head>
    <title>Лабораторная работа №8</title>
</head>
<style>
    table {
        border-collapse: collapse;
        border-width: 1px;
    }
    th, td {
        border: 1px solid black;
        padding: 4px;
    }
    thead {
        background-color: lightgray;
        font-weight: bold;
        font-size: 18px;
        text-align: center;
        font-family: Arial, serif;
    }
    tbody td {
        font-weight: normal;
        font-size: 16px;
        font-family: Arial, serif;
        text-align: center;
    }
    a {
        font-weight: bold;
    }
    .filter-section {
        border: 1px solid black;
        width: 15%;
        padding: 8px;
        margin-bottom: 16px;
        font-family: Arial, serif;
        font-size: 16px;
    }
    .filter-section input[type="submit"] {
        border-radius: 4px;
        height: 32px ;
        font-size: 16px;
        font-family: Arial, serif;
    }
    .filter-section a {
        float: right;
        text-decoration: none;
    }
    .bottom_table {
        margin-top: 10px;
    }
    .bottom_table input[type="submit"] {
        height: 100%;
        text-align: center;
        font-size: 16pt;
        padding: 8px;
        font-family: Arial, serif;
    }
</style>
<body>
    <div class="filter-section">
        <form method="GET" action="index.php">
            <strong>Фильтр по группам:</strong><br>
            <?php foreach ($all_groups as $group_name): ?>
                <label>
                    <input type="checkbox" name="filter_groups[]" value="<?= $group_name ?>"
                            <?= in_array($group_name, $selected_groups) ? 'checked' : '' ?>>
                    <?= $group_name ?>
                </label>
            <?php endforeach; ?>
            <br><br>
            <input type="submit" value="Применить фильтр">
            <a href="index.php">Сбросить</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Номер группы</th>
                <th>Фамилия</th>
                <th>Имя</th>
                <th>Отчество</th>
                <th>Действия</th>
                <th>Экзамены</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetchArray(SQLITE3_ASSOC)):?>
            <tr>
                <td><?= $row['group_name'] ?></td>
                <td><?= $row['family']?></td>
                <td><?= $row['name']?></td>
                <td><?= $row['father_name']?></td>
                <td>
                    <a href="edit.php?id=<?= $row['id'] ?>">Редактировать</a>
                    |
                    <a href="delete.php?id=<?= $row['id'] ?>">Удалить</a>
                </td>
                <td>
                    <a href="exams.php?student_id=<?= $row['id'] ?>" style="color: blue;">Результаты экзаменов</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <form method="post" action="add.php">
        <div class = "bottom_table">
            <input type = "submit" value="Добавить студента">
        </div>
    </form>
</body>