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

if (!isset($_GET['id']) || !isset($_GET['exam_id'])) {
    header("Location: admin.php");
    exit();
}

$id = (int) $_GET['id'];
$exam_id = (int) $_GET['exam_id'];

$message = "";

$result = $mysqli->query("SELECT * FROM exam_QA WHERE id = $id AND exam_id = $exam_id");
if ($result->num_rows == 0) {
    die("Question not found!");
}
$mcq = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $question = $_POST['question'];
    $option1  = $_POST['option1'];
    $option2  = $_POST['option2'];
    $option3  = $_POST['option3'];
    $option4  = $_POST['option4'];
    $answer   = $_POST['answer'];

    $update_sql = "
        UPDATE exam_QA SET
            question = '$question',
            option1 = '$option1',
            option2 = '$option2',
            option3 = '$option3',
            option4 = '$option4',
            answer  = '$answer'
        WHERE id = $id
    ";

    if ($mysqli->query($update_sql)) {
        header("Location: view_qa.php?exam_id=$exam_id");
        exit();
    } else {
        $message = "Error updating question!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question</title>
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
        <div class="col-lg-8">

            <div class="card">
                <div class="card-body">

                    <h4 class="fw-bold mb-1">Edit Question</h4>
                 

                    <?php if ($message): ?>
                        <div class="alert alert-danger"><?php echo $message; ?></div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Question</label>
                            <textarea name="question" class="form-control" rows="3" required><?php echo htmlspecialchars($mcq['question']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 1</label>
                                <input type="text" name="option1" class="form-control"
                                       value="<?php echo htmlspecialchars($mcq['option1']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 2</label>
                                <input type="text" name="option2" class="form-control"
                                       value="<?php echo htmlspecialchars($mcq['option2']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 3</label>
                                <input type="text" name="option3" class="form-control"
                                       value="<?php echo htmlspecialchars($mcq['option3']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Option 4</label>
                                <input type="text" name="option4" class="form-control"
                                       value="<?php echo htmlspecialchars($mcq['option4']); ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Correct Answer</label>
                            <select name="answer" class="form-select" required>
                                <option value="option1" <?php if ($mcq['answer']=="option1") echo "selected"; ?>>Option 1</option>
                                <option value="option2" <?php if ($mcq['answer']=="option2") echo "selected"; ?>>Option 2</option>
                                <option value="option3" <?php if ($mcq['answer']=="option3") echo "selected"; ?>>Option 3</option>
                                <option value="option4" <?php if ($mcq['answer']=="option4") echo "selected"; ?>>Option 4</option>
                            </select>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill">
                                💾 Update Question
                            </button>
                            <a href="view_qa.php?exam_id=<?php echo $exam_id; ?>"
                               class="btn btn-outline-secondary flex-fill">
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
