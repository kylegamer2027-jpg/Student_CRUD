
<?php
require "auth.php";
require "db.php";

$error = "";
$student_number = "";
$full_name = "";
$course = "";
$email = "";

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
                "INSERT INTO students
                (student_number, full_name, course, email)
                VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([
                $student_number, $full_name, $course, $email
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            $error = $e->getCode() === "23000"
                ? "That student number may already exist."
                : "Unable to save the record.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-card">
    <h1>Add Student</h1>
    <p>Enter the student's information below.</p>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Student Number</label>
        <input name="student_number" required maxlength="30"
               value="<?= htmlspecialchars($student_number) ?>">

        <label>Full Name</label>
        <input name="full_name" required maxlength="100"
               value="<?= htmlspecialchars($full_name) ?>">

        <label>Course</label>
        <input name="course" required maxlength="100"
               placeholder="BS Information Technology"
               value="<?= htmlspecialchars($course) ?>">

        <label>Email</label>
        <input type="email" name="email" required maxlength="100"
               value="<?= htmlspecialchars($email) ?>">

        <button class="btn" type="submit">Save Student</button>
        <a class="btn-cancel" href="index.php">Cancel</a>
    </form>
</div>
</body>
</html>
