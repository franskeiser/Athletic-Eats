<?php
require_once 'db.php';

// Fetch all recipes ordered by newest first
$stmt    = $pdo->query('SELECT id, title, category, calories, protein, carbs, fat FROM recipes ORDER BY created_at DESC');
$recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Manager — AthleticEats</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <style>
        .admin-wrap {
            max-width: 1000px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .admin-header h1 {
            font-size: 1.8rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .admin-header h1 span { color: var(--accent-coral); }

        table {
            width: 100%;
            border-collapse: collapse;
            background: var(--white);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--card-shadow);
        }
        thead { background: var(--accent-coral); color: var(--white); }
        th, td {
            padding: 0.85rem 1.1rem;
            text-align: left;
            font-size: 0.9rem;
        }
        th { font-weight: 700; letter-spacing: 0.02em; }
        tbody tr:nth-child(even) { background: var(--bg-color); }
        tbody tr:hover { background: var(--accent-coral-light); }

        .badge {
            display: inline-block;
            padding: 0.2rem 0.7rem;
            border-radius: var(--radius-pill);
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: capitalize;
            background: var(--accent-coral-light);
            color: var(--accent-coral);
        }

        .action-links { display: flex; gap: 0.5rem; }
        .btn-edit, .btn-delete {
            padding: 0.35rem 0.9rem;
            border-radius: var(--radius-sm);
            font-size: 0.82rem;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
        }
        .btn-edit  { background: #e8f4fd; color: #1a73e8; }
        .btn-edit:hover  { background: #1a73e8; color: #fff; }
        .btn-delete { background: #fdecea; color: #d93025; }
        .btn-delete:hover { background: #d93025; color: #fff; }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-muted);
        }
        .empty-state p { font-size: 1.05rem; margin-bottom: 1.5rem; }
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
    </ul>
</nav>

<div class="admin-wrap">
    <div class="admin-header">
        <h1>Recipe <span>Manager</span></h1>
        <a href="add.php" class="btn-primary">+ Add New Recipe</a>
    </div>

    <?php if (empty($recipes)): ?>
        <div class="empty-state">
            <p>No recipes yet. Add your first one!</p>
            <a href="add.php" class="btn-primary">+ Add New Recipe</a>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Cal</th>
                    <th>Protein</th>
                    <th>Carbs</th>
                    <th>Fat</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recipes as $recipe): ?>
                <tr>
                    <td><?= $recipe['id'] ?></td>
                    <td><?= htmlspecialchars($recipe['title']) ?></td>
                    <td><span class="badge"><?= htmlspecialchars($recipe['category']) ?></span></td>
                    <td><?= $recipe['calories'] ?> kcal</td>
                    <td><?= $recipe['protein'] ?>g</td>
                    <td><?= $recipe['carbs'] ?>g</td>
                    <td><?= $recipe['fat'] ?>g</td>
                    <td>
                        <div class="action-links">
                            <a href="edit.php?id=<?= $recipe['id'] ?>" class="btn-edit">Edit</a>
                            <a href="delete.php?id=<?= $recipe['id'] ?>" class="btn-delete">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<footer><p>&copy; <?= date('Y') ?> AthleticEats. All rights reserved.</p></footer>
</body>
</html>
