
<?php
require "auth.php";
require "db.php";
require "functions.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!verify_csrf()) {
    http_response_code(403);
    exit("Invalid security token.");
}

if (!$id || $id < 1) {
    exit("Invalid student ID.");
}

$stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
$stmt->execute([$id]);

set_flash("success", "Student deleted successfully.");
header("Location: index.php");
exit;
?>
