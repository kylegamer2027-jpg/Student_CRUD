
<?php
require "auth.php";
require "db.php";

$stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
$students = $stmt->fetchAll();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Management System</title>

    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
</head>
<body>

<div class="container">

    <!-- Dashboard Header -->
    <header>
        <div>
            <h1>Student Management</h1>
            <p>
                Welcome,
                <?= e($_SESSION["user_name"] ?? "User") ?>!
                Manage your student records in one place.
            </p>
        </div>

        <div class="header-actions">

            <!-- Dark Mode Toggle -->
            <button
                type="button"
                class="theme-btn"
                onclick="toggleTheme()"
                aria-label="Toggle dark mode"
            >
                <span class="theme-icon">🌙</span>
                <span>Dark Mode</span>
            </button>

            <!-- Add Student -->
            <a class="btn" href="create.php">
                + Add Student
            </a>

            <!-- Logout -->
            <a
                class="btn-logout"
                href="logout.php"
                onclick="return confirm('Are you sure you want to log out?');"
            >
                Logout
            </a>

        </div>
    </header>

    <!-- Student Records -->
    <section class="card">

        <h2>Student Records</h2>

        <p>
            Total students: <strong><?= count($students) ?></strong>
        </p>

        <div class="table-wrap">

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student Number</th>
                        <th>Full Name</th>
                        <th>Course</th>
                        <th>Email</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                <?php if (empty($students)): ?>

                    <tr>
                        <td colspan="6">
                            No student records yet.
                            Click "Add Student" to create your first record.
                        </td>
                    </tr>

                <?php else: ?>

                    <?php foreach ($students as $student): ?>

                    <tr>
                        <td><?= e($student["id"]) ?></td>

                        <td><?= e($student["student_number"]) ?></td>

                        <td><?= e($student["full_name"]) ?></td>

                        <td><?= e($student["course"]) ?></td>

                        <td><?= e($student["email"]) ?></td>

                        <td class="actions">

                            <!-- Edit Student -->
                            <a
                                class="btn-edit"
                                href="edit.php?id=<?= (int)$student["id"] ?>"
                            >
                                Edit
                            </a>

                            <!-- Delete Student -->
                            <form
                                action="delete.php"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this student?');"
                            >
                                <input
                                    type="hidden"
                                    name="id"
                                    value="<?= (int)$student["id"] ?>"
                                >

                                <button
                                    class="btn-delete"
                                    type="submit"
                                >
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>

                    <?php endforeach; ?>

                <?php endif; ?>

                </tbody>
            </table>

        </div>

    </section>

    <!-- Dashboard Footer -->
    <footer style="text-align: center; margin-top: 25px;">
        <p>
            Student Management System &copy; <?= date("Y") ?>
        </p>
    </footer>

</div>

</body>
</html>
