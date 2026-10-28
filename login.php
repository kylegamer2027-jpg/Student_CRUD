
<?php
session_start();
require "db.php";

if (!empty($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

$error = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $pdo->prepare(
        "SELECT id, full_name, email, password_hash
         FROM users WHERE email = ? LIMIT 1"
    );
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user["password_hash"])) {
        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["user_name"] = $user["full_name"];
        $_SESSION["user_email"] = $user["email"];

        header("Location: index.php");
        exit;
    }

    $error = "Incorrect email or password.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>
<div class="form-card">
    <div class="form-top">
        <h1>Welcome Back</h1>
        <button type="button" class="theme-btn" onclick="toggleTheme()">
            <span class="theme-icon">🌙</span>
        </button>
    </div>

    <p>Login to your Student Management System.</p>

    <?php if (isset($_GET["registered"])): ?>
        <p class="success">Account created! You can now log in.</p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Email Address</label>
        <input type="email" name="email" required
               autocomplete="email"
               value="<?= htmlspecialchars($email) ?>">

        <label>Password</label>
        <input type="password" name="password"
               autocomplete="current-password" required>

        <button class="btn full-btn" type="submit">Login</button>
    </form>

    <p class="auth-link">
        Don't have an account?
        <a href="register.php">Register here</a>
    </p>
</div>
</body>
</html>
