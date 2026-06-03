<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');
require_once 'db.php';

$query    = trim($_GET['q']        ?? '');
$category = trim($_GET['category'] ?? '');

$sql    = 'SELECT id, title, category, description, image,
                  calories, protein, carbs, fat, ingredients, steps
           FROM recipes WHERE 1=1';
$params = [];

if ($query !== '') {
    $sql .= ' AND title LIKE :query';
    $params[':query'] = '%' . $query . '%';
}

$allowed = ['breakfast', 'lunch', 'dinner', 'snacks'];
if ($category !== '' && in_array($category, $allowed, true)) {
    $sql .= ' AND category = :category';
    $params[':category'] = $category;
}

$sql .= ' ORDER BY category ASC, title ASC';

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $recipes = array_map(function ($r) {
        $ingredients = array_values(array_filter(array_map('trim', explode("\n", $r['ingredients'] ?? ''))));
        $steps       = array_values(array_filter(array_map('trim', explode("\n", $r['steps']       ?? ''))));

        return [
            'id'          => (string)$r['id'],
            'title'       => $r['title'],
            'category'    => $r['category'],
            'description' => $r['description'] ?? '',
            'image'       => !empty($r['image']) ? 'images/' . $r['image'] : '',
            'macros'      => [
                'cal'  => (int)$r['calories'],
                'pro'  => (int)$r['protein'],
                'carb' => (int)$r['carbs'],
                'fat'  => (int)$r['fat'],
            ],
            'ingredients' => $ingredients,
            'steps'       => $steps,
        ];
    }, $rows);

    echo json_encode([
        'success'  => true,
        'count'    => count($recipes),
        'query'    => $query,
        'category' => $category,
        'recipes'  => $recipes,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Something went wrong. Please try again.',
        'recipes' => [],
        'count'   => 0,
    ]);
}
