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

if (!isset($_GET['exam_id'])) {
    header("Location: admin.php");
    exit();
}

$exam_id = (int) $_GET['exam_id']; 
$exam_result = $mysqli->query("SELECT exam FROM exam_name WHERE id = $exam_id");
if ($exam_result->num_rows == 0) {
    die("Exam not found!");
}
$exam = $exam_result->fetch_assoc()['exam'];

$mcq_result = $mysqli->query(
    "SELECT * FROM exam_QA WHERE exam_id = $exam_id ORDER BY id ASC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Questions - <?php echo htmlspecialchars($exam); ?></title>
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
        .table th, .table td {
            vertical-align: middle;
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

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0">
            Questions – <span class="text-primary"><?php echo htmlspecialchars($exam); ?></span>
        </h4>
        <div>
            <a href="add_qa.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-success btn-sm">
                ➕ Add Question
            </a>
        </div>
    </div>

    <!-- TABLE CARD -->
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Option 1</th>
                            <th>Option 2</th>
                            <th>Option 3</th>
                            <th>Option 4</th>
                            <th>Correct</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php if ($mcq_result->num_rows > 0): ?>
                        <?php $i = 1; while ($row = $mcq_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($row['question']); ?></td>
                            <td><?php echo htmlspecialchars($row['option1']); ?></td>
                            <td><?php echo htmlspecialchars($row['option2']); ?></td>
                            <td><?php echo htmlspecialchars($row['option3']); ?></td>
                            <td><?php echo htmlspecialchars($row['option4']); ?></td>

                            <td>
                                <span class="badge bg-info">
                                    <?php echo htmlspecialchars($row[$row['answer']]); ?>
                                </span>
                            </td>

                            <td class="text-center">
                                <a href="edit_qa.php?id=<?php echo $row['id']; ?>&exam_id=<?php echo $exam_id; ?>"
                                   class="btn btn-sm btn-warning mb-1">Edit</a>

                                <?php if ($row['status'] == 1): ?>
                                    <a href="toggle_status.php?id=<?php echo $row['id']; ?>&exam_id=<?php echo $exam_id; ?>&status=0"
                                       class="btn btn-sm btn-success mb-1"
                                       onclick="return confirm('Deactivate this question?')">
                                       Active
                                    </a>
                                <?php else: ?>
                                    <a href="toggle_status.php?id=<?php echo $row['id']; ?>&exam_id=<?php echo $exam_id; ?>&status=1"
                                       class="btn btn-sm btn-secondary mb-1"
                                       onclick="return confirm('Activate this question?')">
                                       Inactive
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">
                                No questions added yet.
                            </td>
                        </tr>
                    <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                <a href="admin.php" class="btn btn-outline-secondary">
                    ⬅ Back to Dashboard
                </a>
            </div>

        </div>
    </div>

</div>

</body>
</html>
