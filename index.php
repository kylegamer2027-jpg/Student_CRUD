
<?php
require "auth.php";
require "db.php";
require "functions.php";

$search = trim($_GET["search"] ?? "");
$course_filter = trim($_GET["course"] ?? "");

$where = [];
$params = [];

if ($search !== "") {
    $where[] = "(student_number LIKE ? OR full_name LIKE ? OR email LIKE ?)";
    $searchTerm = "%" . $search . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($course_filter !== "") {
    $where[] = "course = ?";
    $params[] = $course_filter;
}

$sql = "SELECT * FROM students";
if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll();

$courseRows = $pdo->query(
    "SELECT course, COUNT(*) AS total FROM students GROUP BY course ORDER BY course"
)->fetchAll();
$totalStudents = (int)$pdo->query("SELECT COUNT(*) FROM students")->fetchColumn();
$courseCount = count($courseRows);
$flash = get_flash();
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Management System</title>

    <link rel="stylesheet" href="style.css">
    <script src="theme.js"></script>
    <script src="app.js" defer></script>
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

    <?php if ($flash): ?>
        <div class="toast <?= e($flash["type"]) ?>" role="status">
            <?= e($flash["message"]) ?>
        </div>
    <?php endif; ?>

    <section class="stats-grid" aria-label="Student statistics">
        <article class="stat-card">
            <span class="stat-label">Total Students</span>
            <strong class="stat-value" data-count="<?= $totalStudents ?>">0</strong>
        </article>
        <article class="stat-card">
            <span class="stat-label">Courses</span>
            <strong class="stat-value" data-count="<?= $courseCount ?>">0</strong>
        </article>
        <article class="stat-card">
            <span class="stat-label">Showing</span>
            <strong class="stat-value" data-count="<?= count($students) ?>">0</strong>
        </article>
    </section>

    <!-- Student Records -->
    <section class="card">

        <div class="records-heading">
            <div>
                <h2>Student Records</h2>
                <p>Search, filter, edit, or remove student profiles.</p>
            </div>
        </div>

        <form class="filter-bar" method="GET">
            <div class="search-field">
                <span aria-hidden="true">⌕</span>
                <input
                    id="studentSearch"
                    name="search"
                    type="search"
                    placeholder="Search name, number, or email"
                    value="<?= e($search) ?>"
                >
            </div>
            <select name="course" aria-label="Filter by course">
                <option value="">All courses</option>
                <?php foreach ($courseRows as $course): ?>
                    <option value="<?= e($course["course"]) ?>" <?= $course_filter === $course["course"] ? "selected" : "" ?>>
                        <?= e($course["course"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn" type="submit">Filter</button>
            <?php if ($search !== "" || $course_filter !== ""): ?>
                <a class="btn-cancel" href="index.php">Clear</a>
            <?php endif; ?>
        </form>

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
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
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
