<?php
session_start();
include("db.php");

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'], $_GET['status'], $_GET['exam_id'])) {
    header("Location: admin.php");
    exit();
}

$id = (int) $_GET['id'];
$status = (int) $_GET['status'];
$exam_id = (int) $_GET['exam_id'];

$mysqli->query("UPDATE exam_QA SET status = $status WHERE id = $id");

header("Location: view_qa.php?exam_id=$exam_id");
exit();
