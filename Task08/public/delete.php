<?php
$db = new SQLite3("../data/students_database.db");
$id = $_GET['id'];
$res = $db->query("SELECT * FROM student WHERE id = $id");
$row = $res->fetchArray(SQLITE3_ASSOC);
$db->exec("DELETE FROM student_card WHERE id_student = $id");
$db->exec("DELETE FROM student WHERE id = $id");
header("Location: index.php");
exit;
?>