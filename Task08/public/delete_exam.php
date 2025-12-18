<?php
$db = new SQLite3("../data/students_database.db");
$db->exec("DELETE FROM exam WHERE id = " . $_GET['id']);
header("Location: exams.php?student_id=" . $_GET['student_id']);