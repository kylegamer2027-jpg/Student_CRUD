
<?php
session_start();
require "db.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$full_name = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($full_name === "" || $email === "" || $password === "") {
        $error = "Please complete all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters.";
    } else {
        try {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare(
                "INSERT INTO users (full_name, email, password_hash)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$full_name, $email, $hash]);

            header("Location: login.php?registered=1");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === "23000") {
                $error = "That email is already registered.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="form-card">
    <div class="form-top">
        <h1>Create Account</h1>
        <button type="button" class="theme-btn" onclick="toggleTheme()">
            <span class="theme-icon">🌙</span>
        </button>
    </div>

    <p>Register to manage student records.</p>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Full Name</label>
        <input name="full_name" maxlength="100" required
               autocomplete="name"
               value="<?= htmlspecialchars($full_name) ?>">

        <label>Email Address</label>
        <input type="email" name="email" maxlength="150" required
               autocomplete="email"
               value="<?= htmlspecialchars($email) ?>">

        <label>Password</label>
        <input type="password" name="password" minlength="8"
               autocomplete="new-password" required>

        <button class="btn full-btn" type="submit">Register</button>
    </form>

    <p class="auth-link">
        Already have an account?
        <a href="login.php">Login here</a>
    </p>
</div>
</body>
</html>
