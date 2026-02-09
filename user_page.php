<?php
session_start();
include("db.php");

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

// Determine what to display: all exams or attended exams
$view = isset($_GET['view']) && $_GET['view'] == 'attended' ? 'attended' : 'all';

// Fetch exams
if ($view === 'attended') {
    $exams_result = $mysqli->query("
        SELECT e.*, a.score 
        FROM exam_name e
        JOIN exam_attendance a ON e.id = a.exam_id
        WHERE a.user_id = '$user_id'
        ORDER BY e.id ASC
    ");
} else {
    $exams_result = $mysqli->query("SELECT * FROM exam_name where e_status=1 ORDER BY id ASC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
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
        .exam-card {
            transition: transform 0.2s ease;
            border-radius: 12px;
        }
        .exam-card:hover {
            transform: translateY(-4px);
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
            <a href="user_page.php?view=all" class="<?php echo $view === 'all' ? 'active' : ''; ?>">Exam List</a>
            <a href="attended_exam.php">Attended Exams</a>
        </div>

        <!-- Main content -->
        <div class="col-md-10 py-4">
            <h4 class="fw-bold mb-4">
                <?php echo $view === 'attended' ? 'Attended Exams' : 'Available Exams'; ?>
            </h4>

            <div class="row g-4">
                <?php if ($exams_result && $exams_result->num_rows > 0): ?>
                    <?php while ($row = $exams_result->fetch_assoc()): 
                        $exam_id = $row['id'];
                        $score = $view === 'attended' ? $row['score'] : null;

                        if ($view === 'all') {
                            $attended_check = $mysqli->query("
                                SELECT score FROM exam_attendance 
                                WHERE user_id = '$user_id' AND exam_id = '$exam_id'
                            ");
                            $is_attended = $attended_check && $attended_check->num_rows > 0;
                            $score = $is_attended ? $attended_check->fetch_assoc()['score'] : null;
                        }
                    ?>
                    <div class="col-md-4">
                        <div class="card exam-card shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['exam']); ?></h5>

                                <?php if ($score !== null): ?>
                                    <span class="badge bg-success mb-2">Completed</span>
                                    <p class="mb-0"><strong>Score:</strong> <?php echo $score; ?></p>
                                <?php elseif ($view === 'all'): ?>
                                    <div class="mt-auto">
                                        <a href="start_exam.php?exam_id=<?php echo $exam_id; ?>" class="btn btn-primary w-100">Start Exam</a>
                                    </div>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">No exams found.</div>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<footer class="text-center py-4 mt-5 text-muted">
    © <?php echo date("Y"); ?> Exam Portal
</footer>

</body>
</html>
