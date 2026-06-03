<?php
header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Cache-Control: no-store');

require_once 'db.php';

// --- Recipes from database ---
$stmt    = $pdo->query('SELECT * FROM recipes ORDER BY category, title');
$rows    = $stmt->fetchAll();

$recipes = array_map(function ($row) {
    // Split newline-separated text back into arrays (same format add/edit forms use)
    $ingredients = array_values(array_filter(array_map('trim', explode("\n", $row['ingredients'] ?? ''))));
    $steps       = array_values(array_filter(array_map('trim', explode("\n", $row['steps']       ?? ''))));

    return [
        'id'          => (string)$row['id'],          
        'title'       => $row['title'],
        'category'    => $row['category'],
        'image'       => 'images/' . $row['image'],   
        'description' => $row['description'] ?? '',
        'macros'      => [
            'cal'  => (int)$row['calories'],
            'pro'  => (int)$row['protein'],
            'carb' => (int)$row['carbs'],
            'fat'  => (int)$row['fat'],
        ],
        'ingredients' => $ingredients,
        'steps'       => $steps,
    ];
}, $rows);

// --- Extras are not in the DB---
$extras = [
    ['id' => 'white-rice',    'title' => 'White Rice (100g)',       'macros' => ['cal' => 130, 'pro' => 2,  'carb' => 28, 'fat' => 0]],
    ['id' => 'brown-rice',    'title' => 'Brown Rice (100g)',        'macros' => ['cal' => 110, 'pro' => 3,  'carb' => 23, 'fat' => 1]],
    ['id' => 'boiled-egg',    'title' => 'Large Boiled Egg',         'macros' => ['cal' => 70,  'pro' => 6,  'carb' => 0,  'fat' => 5]],
    ['id' => 'avocado-half',  'title' => 'Avocado (1/2)',            'macros' => ['cal' => 160, 'pro' => 2,  'carb' => 9,  'fat' => 15]],
    ['id' => 'sweet-potato',  'title' => 'Boiled Kamote (100g)',     'macros' => ['cal' => 86,  'pro' => 2,  'carb' => 20, 'fat' => 0]],
    ['id' => 'protein-shake', 'title' => 'Protein Shake (1 scoop)', 'macros' => ['cal' => 120, 'pro' => 25, 'carb' => 3,  'fat' => 1]],
];

echo json_encode(['recipes' => $recipes, 'extras' => $extras]);
