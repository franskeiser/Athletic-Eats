<?php
require_once __DIR__ . '/auth.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password !== '' && hash_equals(ADMIN_PASSWORD, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        header('Location: index.php');
        exit;
    }
    $error = 'Incorrect password. Please try again.';
    // Brief sleep to blunt brute-force attempts
    sleep(1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — AthleticEats</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; }
        .login-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        .login-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-card h1 { font-size: 1.6rem; font-weight: 800; margin-bottom: 0.3rem; }
        .login-card h1 span { color: var(--accent-coral); }
        .login-card p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.2rem; text-align: left; }
        label { display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; }
        input[type="password"] {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.92rem;
            outline: none;
            transition: border-color 0.2s;
            box-sizing: border-box;
        }
        input[type="password"]:focus { border-color: var(--accent-coral); }
        .error-msg {
            background: #fdecea;
            color: #d93025;
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            font-size: 0.88rem;
            margin-bottom: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 0.9rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
<nav>
    <div class="logo"><a href="../index.html">ATHLETIC<span>EATS</span></a></div>
    <ul>
        <li><a href="../recipes.html">Recipes</a></li>
        <li><a href="../meal-planner.html">Meal Planner</a></li>
        <li><a href="../calculator.html">Calculator</a></li>
    </ul>
</nav>

<div class="login-wrap">
    <div class="login-card">
        <h1>Admin <span>Login</span></h1>
        <p>Enter the admin password to continue.</p>

        <?php if ($error): ?>
            <div class="error-msg"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" autofocus autocomplete="current-password">
            </div>
            <button type="submit" class="btn-primary btn-login">Sign In</button>
        </form>
    </div>
</div>

<footer><p>&copy; <?= date('Y') ?> AthleticEats. All rights reserved.</p></footer>
</body>
</html>
