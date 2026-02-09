<?php
session_start();
include("db.php");

if (!isset($_SESSION['username']) || $_SESSION['user_type'] != 2) {
    header("Location: index.php");
    exit();
}

$username = $_SESSION['username'];
$user_res = $mysqli->query("SELECT id FROM admin WHERE username = '$username' LIMIT 1");
if (!$user_res || $user_res->num_rows == 0) die("User not found.");
$user_id = $user_res->fetch_assoc()['id'];

$exam_id = isset($_GET['exam_id']) ? intval($_GET['exam_id']) : 0;
if ($exam_id <= 0) die("Invalid exam.");


$check = $mysqli->query("SELECT * FROM exam_attendance WHERE user_id = '$user_id' AND exam_id = '$exam_id'");
if (!$check) die("Database error: " . $mysqli->error);
if ($check->num_rows > 0) die("You have already attended this exam.");

$exam_res = $mysqli->query("SELECT exam FROM exam_name WHERE id = '$exam_id'");
if (!$exam_res || $exam_res->num_rows == 0) die("Exam not found.");
$exam = $exam_res->fetch_assoc()['exam'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $answers = $_POST['answers'] ?? [];
    if (empty($answers)) die("Invalid submission.");

    
    $total_res = $mysqli->query("SELECT COUNT(*) AS total FROM exam_QA WHERE exam_id = '$exam_id'");
    $total_questions = $total_res->fetch_assoc()['total'];
    $score = 0;

    
    $qa_result = $mysqli->query("SELECT id, answer FROM exam_QA WHERE exam_id = '$exam_id'");
    if (!$qa_result) die("Database error: " . $mysqli->error);

    while ($q = $qa_result->fetch_assoc()) {
        $question_id = $q['id'];
        $correct_answer = $q['answer'];
        $selected_answer = $answers[$question_id] ?? '';

        $is_correct = ($selected_answer === $correct_answer) ? 1 : 0;
        if ($is_correct) $score++;

        
        $mysqli->query("
            INSERT INTO exam_detail 
            (user_id, exam_id, question_id, selected_answer, correct_answer, is_correct)
            VALUES 
            ('$user_id', '$exam_id', '$question_id', '$selected_answer', '$correct_answer', '$is_correct')
        ");
    }

    
    $percentage = ($score / $total_questions) * 100;

    if ($percentage >= 80) {
        $insert = $mysqli->query("
            INSERT INTO exam_attendance 
            (user_id, exam_id, score, attended_date, status)
            VALUES 
            ('$user_id', '$exam_id', '$score', NOW(), 1)
        ");
        if (!$insert) die("Database error: " . $mysqli->error);
    }

    ?>
    <script>
        <?php if ($percentage >= 80) { ?>
            alert(
                "✅ PASSED!\n\n" +
                "Score: <?php echo $score; ?> / <?php echo $total_questions; ?>\n" +
                "Percentage: <?php echo round($percentage, 2); ?>%"
            );
            window.location.href = "user_page.php";
        <?php } else { ?>
            alert(
                "❌ FAILED!\n\n" +
                "Score: <?php echo $score; ?> / <?php echo $total_questions; ?>\n" +
                "Percentage: <?php echo round($percentage, 2); ?>%\n\n" +
                "Please reattempt the exam."
            );
            window.location.href = "start_exam.php?exam_id=<?php echo $exam_id; ?>";
        <?php } ?>
    </script>
    <?php
    exit();
}


$questions = $mysqli->query("SELECT * FROM exam_QA WHERE exam_id = '$exam_id' AND status = 1");
if (!$questions) die("Database error: " . $mysqli->error);

$_SESSION['exam_start_time'] = date("Y-m-d H:i:s");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($exam); ?> - Exam</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .exam-header { background-color: #0d6efd; color: white; padding: 20px; border-radius: 10px; text-align: center; margin-bottom: 30px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .question-card { border-left: 5px solid #0d6efd; margin-bottom: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.08); }
        .question-card .card-body { padding: 20px; }
        .form-check-label { cursor: pointer; }
        .btn-submit { background-color: #198754; color: white; }
        .btn-submit:hover { background-color: #157347; }
        .btn-back { background-color: #6c757d; color: white; }
        .btn-back:hover { background-color: #5c636a; }
    </style>
</head>
<body>
<div class="container my-5">

    <div class="exam-header">
        <h2><?php echo htmlspecialchars($exam); ?></h2>
        <p class="mb-0">Please answer all questions carefully</p>
    </div>

    <form method="POST" action="">
        <input type="hidden" name="exam_id" value="<?php echo $exam_id; ?>">

        <?php $i = 1; while($q = $questions->fetch_assoc()): ?>
            <div class="card question-card">
                <div class="card-body">
                    <p><strong><?php echo $i . ". " . htmlspecialchars($q['question']); ?></strong></p>
                    <?php for($o=1; $o<=4; $o++): ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="answers[<?php echo $q['id']; ?>]" value="option<?php echo $o; ?>" required>
                            <label class="form-check-label"><?php echo htmlspecialchars($q['option'.$o]); ?></label>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        <?php $i++; endwhile; ?>

        <div class="d-flex flex-column flex-md-row gap-2 mt-4">
            <button type="submit" class="btn btn-submit w-100">Submit Exam</button>
            <a href="user_page.php" class="btn btn-back w-100">Back</a>
        </div>
    </form>
</div>
</body>
</html>
