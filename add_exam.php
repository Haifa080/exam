<?php
session_start();
include("db.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION["username"])) {
    header("Location: index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $exam_name  = trim($_POST["exam"]);
    $start_date = $_POST["start_date"];
    $end_date   = $_POST["end_date"];

    $check_sql = "SELECT id FROM exam_name WHERE exam = '$exam_name' LIMIT 1";
    $check_res = $mysqli->query($check_sql);

    if ($check_res && $check_res->num_rows > 0) {
        $message = "Exam name already exists. Please use a different name.";
    } else {

        $sql = "INSERT INTO exam_name (exam, start_date, end_date, created_date, e_status)
                VALUES ('$exam_name', '$start_date', '$end_date', NOW(), '1')";

        if ($mysqli->query($sql)) {
            header("Location: admin.php");
            exit();
        } else {
            $message = "Error: " . $mysqli->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Exam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<!-- SAME ADMIN HEADER -->
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Admin Panel</span>
    <div class="text-white">
        👤 <?php echo htmlspecialchars($_SESSION["username"]); ?>
         <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Logout</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            <div class="card">
                <div class="card-body">

                    <h4 class="fw-bold mb-1">Add New Exam</h4>

                    <?php if ($message): ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exam Name</label>
                            <input type="text" name="exam" class="form-control" placeholder="Topics" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <button class="btn btn-primary flex-fill">
                                💾 Save Exam
                            </button>
                            <a href="admin.php" class="btn btn-outline-secondary flex-fill">
                                Cancel
                            </a>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
