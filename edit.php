
<?php
require "auth.php";
require "db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    exit("Invalid student ID.");
}

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
$stmt->execute([$id]);
$student = $stmt->fetch();

if (!$student) {
    http_response_code(404);
    exit("Student not found.");
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_number = trim($_POST["student_number"] ?? "");
    $full_name = trim($_POST["full_name"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if (!$student_number || !$full_name || !$course || !$email) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "UPDATE students
                 SET student_number = ?, full_name = ?,
                     course = ?, email = ?
                 WHERE id = ?"
            );
            $stmt->execute([
                $student_number, $full_name, $course, $email, $id
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === "23000"
                ? "That student number may already exist."
                : "Unable to update the record.";
        }
    }
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-card">
    <h1>Edit Student</h1>
    <p>Update the student's information.</p>

    <?php if ($error): ?>
        <p class="error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Student Number</label>
        <input name="student_number" required maxlength="30"
               value="<?= e($_POST["student_number"] ?? $student["student_number"]) ?>">

        <label>Full Name</label>
        <input name="full_name" required maxlength="100"
               value="<?= e($_POST["full_name"] ?? $student["full_name"]) ?>">

        <label>Course</label>
        <input name="course" required maxlength="100"
               value="<?= e($_POST["course"] ?? $student["course"]) ?>">

        <label>Email</label>
        <input type="email" name="email" required maxlength="100"
               value="<?= e($_POST["email"] ?? $student["email"]) ?>">

        <button class="btn" type="submit">Update Student</button>
        <a class="btn-cancel" href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>
