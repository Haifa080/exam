<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['exam_id'])) {
    header("Location: admin.php");
    exit();
}

$exam_id = (int) $_GET['exam_id'];

$result = $mysqli->query("SELECT e_status FROM exam_name WHERE id = '$exam_id'");
$row = $result->fetch_assoc();

$new_status = ($row['e_status'] == 1) ? 0 : 1;

$mysqli->query("UPDATE exam_name SET e_status = '$new_status' WHERE id = '$exam_id'");

header("Location: admin.php");
exit();
