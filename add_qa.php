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

$exam_id = $_GET['exam_id'];

$exam_sql = "SELECT exam FROM exam_name WHERE id = '$exam_id'";
$exam_result = $mysqli->query($exam_sql);

if ($exam_result->num_rows == 0) {
    die("Exam not found!");
}

$exam = $exam_result->fetch_assoc()['exam'];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $question = $_POST['question'];
    $option1  = $_POST['option1'];
    $option2  = $_POST['option2'];
    $option3  = $_POST['option3'];
    $option4  = $_POST['option4'];
    $answer   = $_POST['answer'];

    $sql = "INSERT INTO exam_QA 
            (exam_id, question, option1, option2, option3, option4, answer, created_at,status)
            VALUES ('$exam_id', '$question', '$option1', '$option2', '$option3', '$option4', '$answer', NOW(),1)";

    if ($mysqli->query($sql)) {
        $message = "Question added successfully!";
    } else {
        $message = "Error: " . $mysqli->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add MCQ</title>
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

<!-- SAME HEADER AS ADMIN -->
<nav class="navbar navbar-dark bg-dark px-4">
    <span class="navbar-brand">Admin Panel</span>
    <div class="text-white">
        👤 <?php echo htmlspecialchars($_SESSION["username"]); ?>
       <a href="logout.php" class="btn btn-sm btn-outline-light ms-3">Logout</a>
    </div>
</nav>

<div class="container mt-4">

    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="card">
                <div class="card-body">

                    <h4 class="fw-bold mb-1">
                        Add Question and Answers – <span class="text-primary"><?php echo htmlspecialchars($exam); ?></span>
                    </h4>
                   

                    <?php if ($message): ?>
                        <div class="alert alert-success"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Question</label>
                            <textarea name="question" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 1</label>
                                <input type="text" name="option1" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 2</label>
                                <input type="text" name="option2" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 3</label>
                                <input type="text" name="option3" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 4</label>
                                <input type="text" name="option4" class="form-control" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Correct Answer</label>
                            <select name="answer" class="form-select" required>
                                <option value="">Select correct option</option>
                                <option value="option1">Option 1</option>
                                <option value="option2">Option 2</option>
                                <option value="option3">Option 3</option>
                                <option value="option4">Option 4</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-success flex-fill">
                                ➕ Add Question
                            </button>
                            <a href="admin.php" class="btn btn-outline-secondary flex-fill">
                                ⬅ Back
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
