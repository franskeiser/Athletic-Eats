<?php
require_once 'db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php'); exit; }

// Fetch existing recipe
$stmt = $pdo->prepare('SELECT * FROM recipes WHERE id = :id');
$stmt->execute([':id' => $id]);
$recipe = $stmt->fetch();
if (!$recipe) { header('Location: index.php'); exit; }

$errors = [];
$fields = [
    'title'       => $recipe['title'],
    'category'    => $recipe['category'],
    'description' => $recipe['description'],
    'image'       => $recipe['image'],
    'calories'    => $recipe['calories'],
    'protein'     => $recipe['protein'],
    'carbs'       => $recipe['carbs'],
    'fat'         => $recipe['fat'],
    'ingredients' => $recipe['ingredients'],
    'steps'       => $recipe['steps'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Sanitize and collect text input; keep current image until we know a new one was uploaded
    foreach ($fields as $key => $_) {
        if ($key !== 'image') {
            $fields[$key] = trim($_POST[$key] ?? '');
        }
    }

    // --- Validate text fields ---
    if ($fields['title'] === '') {
        $errors['title'] = 'Recipe title is required.';
    } elseif (strlen($fields['title']) > 255) {
        $errors['title'] = 'Title must be 255 characters or fewer.';
    }

    $allowed_categories = ['breakfast', 'lunch', 'dinner', 'snacks'];
    if (!in_array($fields['category'], $allowed_categories, true)) {
        $errors['category'] = 'Please select a valid category.';
    }

    foreach (['calories', 'protein', 'carbs', 'fat'] as $macro) {
        if ($fields[$macro] === '') {
            $errors[$macro] = ucfirst($macro) . ' is required.';
        } elseif (!is_numeric($fields[$macro]) || (float)$fields[$macro] < 0) {
            $errors[$macro] = ucfirst($macro) . ' must be a number (0 or more).';
        }
    }

    // --- Handle image upload ---
    $upload_dir = __DIR__ . '/../images/';

    if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['image'] = 'Upload failed. Please try again.';
        } else {
            $allowed_mime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($file['tmp_name']);

            if (!in_array($mime, $allowed_mime, true)) {
                $errors['image'] = 'Only JPG, PNG, GIF, WEBP, or AVIF images are allowed.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $errors['image'] = 'Image must be smaller than 5 MB.';
            } else {
                $original_name  = basename($file['name']);
                $safe_name      = strtolower(preg_replace('/[^a-zA-Z0-9._-]/', '_', $original_name));
                $destination    = $upload_dir . $safe_name;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $fields['image'] = $safe_name; // update to new filename
                } else {
                    $errors['image'] = 'Could not save the image. Check that the /images/ folder is writable.';
                }
            }
        }
    }
    // If no new file, $fields['image'] still holds the original filename from the DB

    // --- Update if no errors ---
    if (empty($errors)) {
        $sql = 'UPDATE recipes SET
                    title       = :title,
                    category    = :category,
                    description = :description,
                    image       = :image,
                    calories    = :calories,
                    protein     = :protein,
                    carbs       = :carbs,
                    fat         = :fat,
                    ingredients = :ingredients,
                    steps       = :steps
                WHERE id = :id';

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':title'       => $fields['title'],
            ':category'    => $fields['category'],
            ':description' => $fields['description'],
            ':image'       => $fields['image'],
            ':calories'    => (int)$fields['calories'],
            ':protein'     => (int)$fields['protein'],
            ':carbs'       => (int)$fields['carbs'],
            ':fat'         => (int)$fields['fat'],
            ':ingredients' => $fields['ingredients'],
            ':steps'       => $fields['steps'],
            ':id'          => $id,
        ]);

        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Recipe — AthleticEats</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <style>
        .form-wrap {
            max-width: 720px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }
        .form-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 2.5rem;
        }
        .form-card h1 { font-size: 1.6rem; font-weight: 800; margin-bottom: 0.3rem; letter-spacing: -0.4px; }
        .form-card h1 span { color: var(--accent-coral); }
        .subtitle { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }

        .form-group { margin-bottom: 1.3rem; }
        label { display: block; font-weight: 600; font-size: 0.88rem; margin-bottom: 0.4rem; }
        .required { color: var(--accent-coral); margin-left: 2px; }

        input[type="text"], input[type="number"], select, textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: inherit;
            font-size: 0.92rem;
            color: var(--text-main);
            background: var(--bg-color);
            transition: border-color 0.2s;
            outline: none;
        }
        input:focus, select:focus, textarea:focus { border-color: var(--accent-coral); background: var(--white); }
        .error-field { border-color: #d93025 !important; }
        .error-msg   { color: #d93025; font-size: 0.82rem; margin-top: 0.3rem; }

        /* Current image preview */
        .current-image-wrap {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--bg-color);
            border-radius: var(--radius-sm);
            padding: 0.75rem 1rem;
            margin-bottom: 0.75rem;
        }
        .current-image-wrap img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
        }
        .current-image-wrap span { font-size: 0.85rem; color: var(--text-muted); }

        /* File upload */
        .upload-box {
            border: 2px dashed var(--border);
            border-radius: var(--radius-sm);
            padding: 1.2rem;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            background: var(--bg-color);
        }
        .upload-box:hover { border-color: var(--accent-coral); background: var(--accent-coral-light); }
        .upload-box input[type="file"] { display: none; }
        .upload-box label { cursor: pointer; font-weight: 600; color: var(--accent-coral); display: block; margin: 0; }
        .upload-box .upload-hint { color: var(--text-muted); font-size: 0.82rem; margin-top: 0.3rem; }
        #preview-wrap { margin-top: 0.75rem; }
        #image-preview { max-height: 160px; border-radius: var(--radius-sm); display: none; }
        #file-name { font-size: 0.82rem; color: var(--text-muted); margin-top: 0.3rem; }

        .macro-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; }
        textarea { resize: vertical; min-height: 110px; }

        .form-actions { display: flex; gap: 1rem; margin-top: 2rem; }
        .btn-cancel {
            padding: 0.95rem 2rem;
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
    </ul>
</nav>

<div class="form-wrap">
    <div class="form-card">
        <h1>Edit <span>Recipe</span></h1>
        <p class="subtitle">Editing: <strong><?= htmlspecialchars($recipe['title']) ?></strong> &mdash; Fields marked <span style="color:var(--accent-coral)">*</span> are required.</p>

        <form method="POST" action="edit.php?id=<?= $id ?>" enctype="multipart/form-data" novalidate>

            <div class="form-group">
                <label for="title">Recipe Title <span class="required">*</span></label>
                <input type="text" id="title" name="title"
                       value="<?= htmlspecialchars($fields['title']) ?>"
                       class="<?= isset($errors['title']) ? 'error-field' : '' ?>">
                <?php if (isset($errors['title'])): ?><div class="error-msg"><?= $errors['title'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="category">Category <span class="required">*</span></label>
                <select id="category" name="category" class="<?= isset($errors['category']) ? 'error-field' : '' ?>">
                    <option value="">— Select a category —</option>
                    <?php foreach (['breakfast', 'lunch', 'dinner', 'snacks'] as $cat): ?>
                        <option value="<?= $cat ?>" <?= $fields['category'] === $cat ? 'selected' : '' ?>><?= ucfirst($cat) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (isset($errors['category'])): ?><div class="error-msg"><?= $errors['category'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description"
                       value="<?= htmlspecialchars($fields['description']) ?>">
            </div>

            <!-- Image Upload with current image preview -->
            <div class="form-group">
                <label>Recipe Image</label>

                <?php if (!empty($fields['image'])): ?>
                <div class="current-image-wrap">
                    <img src="../images/<?= htmlspecialchars($fields['image']) ?>"
                         alt="<?= htmlspecialchars($fields['title']) ?>"
                         onerror="this.style.display='none'">
                    <span>Current: <strong><?= htmlspecialchars($fields['image']) ?></strong><br>Upload a new image below to replace it.</span>
                </div>
                <?php endif; ?>

                <div class="upload-box" onclick="document.getElementById('image').click()">
                    <input type="file" id="image" name="image" accept="image/*">
                    <label for="image">&#128247; Click to choose a new image</label>
                    <div class="upload-hint">JPG, PNG, WEBP, AVIF &mdash; max 5 MB. Leave empty to keep current.</div>
                </div>
                <div id="preview-wrap">
                    <img id="image-preview" src="" alt="Preview">
                    <div id="file-name"></div>
                </div>
                <?php if (isset($errors['image'])): ?><div class="error-msg"><?= $errors['image'] ?></div><?php endif; ?>
            </div>

            <div class="form-group">
                <label>Macros <span class="required">*</span></label>
                <div class="macro-grid">
                    <?php foreach (['calories' => 'Calories (kcal)', 'protein' => 'Protein (g)', 'carbs' => 'Carbs (g)', 'fat' => 'Fat (g)'] as $key => $label): ?>
                    <div>
                        <label for="<?= $key ?>" style="font-weight:500;font-size:0.82rem;"><?= $label ?></label>
                        <input type="number" id="<?= $key ?>" name="<?= $key ?>" min="0"
                               value="<?= htmlspecialchars($fields[$key]) ?>"
                               class="<?= isset($errors[$key]) ? 'error-field' : '' ?>">
                        <?php if (isset($errors[$key])): ?><div class="error-msg"><?= $errors[$key] ?></div><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="ingredients">Ingredients</label>
                <textarea id="ingredients" name="ingredients"><?= htmlspecialchars($fields['ingredients']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="steps">Cooking Steps</label>
                <textarea id="steps" name="steps"><?= htmlspecialchars($fields['steps']) ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="index.php" class="btn-cancel">Cancel</a>
            </div>

        </form>
    </div>
</div>

<footer><p>&copy; <?= date('Y') ?> AthleticEats. All rights reserved.</p></footer>

<script>
    document.getElementById('image').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const preview  = document.getElementById('image-preview');
        const fileName = document.getElementById('file-name');
        preview.src    = URL.createObjectURL(file);
        preview.style.display = 'block';
        fileName.textContent  = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
    });
</script>
</body>
</html>
