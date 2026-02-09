<?php
session_start();
include("db.php");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 1) {
    header("Location: index.php");
    exit();
}

$exams_result = $mysqli->query("SELECT * FROM exam_name ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
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
        .badge-status {
            font-size: 0.85rem;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Admin Panel</span>
    <div class="text-white">
        👤 <?php echo $_SESSION["username"]; ?>
        <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Logout</a>
    </div>
</nav>

<div class="container mt-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Exams Management</h4>
        <a href="add_exam.php" class="btn btn-primary">
            ➕ Add New Exam
        </a>
    </div>

    <!-- TABLE CARD -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">

                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Exam Name</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php $n = 1; ?>
                    <?php while ($row = $exams_result->fetch_assoc()): ?>

                        <tr>
                            <td><?php echo $n++; ?></td>
                            <td class="fw-semibold"><?php echo $row['exam']; ?></td>
                            <td><?php echo $row['start_date']; ?></td>
                            <td><?php echo $row['end_date']; ?></td>
                            <td><?php echo $row['created_date']; ?></td>

                            <td class="text-center">
                                <a href="add_qa.php?exam_id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-success mb-1">Add QA</a>

                                <a href="view_qa.php?exam_id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm btn-info mb-1 text-white">View QA</a>

                                <a href="toggle_exam_status.php?exam_id=<?php echo $row['id']; ?>"
                                   class="btn btn-sm <?php echo ($row['e_status'] == 1) ? 'btn-warning' : 'btn-outline-success'; ?>"
                                   onclick="return confirm('Change exam status?')">

                                    <?php echo ($row['e_status'] == 1) ? 'Deactivate' : 'Activate'; ?>
                                </a>
                            </td>
                        </tr>

                    <?php endwhile; ?>

                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

</body>
</html>
