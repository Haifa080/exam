<?php
session_start();
include("db.php");

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Check if user is logged in and is a regular user
if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 2) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$user_res = $mysqli->query("SELECT id FROM admin WHERE username = '$username' LIMIT 1");
if (!$user_res || $user_res->num_rows == 0) {
    die("User not found.");
}
$user_id = $user_res->fetch_assoc()['id'];

// Fetch attended exams with total score
$attended_result = $mysqli->query("
    SELECT e.exam, e.id AS exam_id, a.score, a.attended_date
    FROM exam_attendance a
    JOIN exam_name e ON a.exam_id = e.id
    WHERE a.user_id = '$user_id'
    ORDER BY a.attended_date DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attended Exams</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: #fff;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 12px 20px;
            text-decoration: none;
            transition: 0.2s;
        }
        .sidebar a.active, .sidebar a:hover {
            background: #495057;
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Exam Portal</span>
    <div class="text-white">
        👤 <?php echo htmlspecialchars($_SESSION["username"]); ?>
        <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Logout</a>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-2 sidebar py-4">
            <h5 class="px-3 mb-3">Menu</h5>
            <a href="user_page.php?view=all">Exam List</a>
            <a href="attended_exam.php" class="active">Attended Exams</a>
        </div>

        <!-- Main content -->
        <div class="col-md-10 py-4">
            <h4 class="fw-bold mb-4">Attended Exams</h4>

            <?php if ($attended_result && $attended_result->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Exam Name</th>
                                <th>Score</th>
                                <th>Out Of</th>
                                <th>Attended Date</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $i = 1; while ($row = $attended_result->fetch_assoc()): 
                            $total_res = $mysqli->query("SELECT COUNT(*) AS total FROM exam_QA WHERE exam_id = ".$row['exam_id']);
                            $total_score = ($total_res && $total_res->num_rows > 0) ? $total_res->fetch_assoc()['total'] : 0;
                        ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo htmlspecialchars($row['exam']); ?></td>
                                <td><?php echo htmlspecialchars($row['score']); ?></td>
                                <td><?php echo $total_score; ?></td>
                                <td><?php echo date("d-m-Y H:i", strtotime($row['attended_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">You have not attended any exams yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<footer class="text-center py-4 mt-5 text-muted">
    © <?php echo date("Y"); ?> Exam Portal
</footer>

</body>
</html>
