<?php
require_once __DIR__ . '/auth.php';
require_login();
require_once __DIR__ . '/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: index.php');
    exit;
}

// Fetch recipe name to show in the confirmation message
$stmt = $pdo->prepare('SELECT id, title FROM recipes WHERE id = :id');
$stmt->execute([':id' => $id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    header('Location: index.php');
    exit;
}

// --- Handle confirmed deletion ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_validate()) {
        http_response_code(403);
        die('Invalid CSRF token. Please go back and try again.');
    }
    $stmt = $pdo->prepare('DELETE FROM recipes WHERE id = :id');
    $stmt->execute([':id' => $id]);

    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Recipe — AthleticEats</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <style>
        .confirm-wrap {
            max-width: 480px;
            margin: 5rem auto;
            padding: 0 1.5rem;
            text-align: center;
        }
        .confirm-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 3rem 2.5rem;
        }
        .confirm-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }
        .confirm-card h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
        }
        .confirm-card p {
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.7;
        }
        .confirm-card strong { color: var(--text-main); }
        .confirm-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        .btn-danger {
            display: inline-block;
            background: #d93025;
            color: #fff;
            padding: 0.9rem 2rem;
            border-radius: var(--radius-pill);
            font-weight: 700;
            font-size: 0.95rem;
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }
        .btn-danger:hover {
            background: #b7261d;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(217, 48, 37, 0.35);
        }
        .btn-cancel {
            display: inline-block;
            padding: 0.9rem 2rem;
            border-radius: var(--radius-pill);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            border: 2px solid var(--border);
            color: var(--text-muted);
            background: transparent;
            transition: var(--transition);
        }
        .btn-cancel:hover { border-color: var(--text-main); color: var(--text-main); }
    </style>
</head>
<body>
<nav>
    <div class="logo"><a href="../index.html">ATHLETIC<span>EATS</span></a></div>
    <ul>
        <li><a href="../recipes.html">Recipes</a></li>
        <li><a href="../meal-planner.html">Meal Planner</a></li>
        <li><a href="../calculator.html">Calculator</a></li>
        <li><a href="index.php" style="color:var(--accent-coral);background:var(--accent-coral-light);">Admin</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<div class="confirm-wrap">
    <div class="confirm-card">
        <div class="confirm-icon">&#x26A0;</div>
        <h1>Delete Recipe?</h1>
        <p>
            You are about to permanently delete<br>
            <strong>"<?= htmlspecialchars($recipe['title']) ?>"</strong>.<br>
            This action cannot be undone.
        </p>

        <div class="confirm-actions">
            <!-- POST form so the deletion is triggered by a form submit, not a GET link -->
            <form method="POST" action="delete.php?id=<?= $id ?>">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                <button type="submit" class="btn-danger">Yes, Delete</button>
            </form>
            <a href="index.php" class="btn-cancel">Cancel</a>
        </div>
    </div>
</div>

<footer><p>&copy; <?= date('Y') ?> AthleticEats. All rights reserved.</p></footer>
</body>
</html>
