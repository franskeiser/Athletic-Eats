<?php
/**
 * seed.php — One-time import of all existing AthleticEats recipes into MySQL.
 * Run this ONCE at http://localhost/AthleticEats/backend/seed.php
 */
require_once 'db.php';

$recipes = [
    [
        'title'       => 'Salmon Buddha Bowl',
        'category'    => 'lunch',
        'description' => 'Rich in Omega-3s and complex carbs for sustained energy.',
        'image'       => 'salmon.avif',
        'calories'    => 520, 'protein' => 42, 'carbs' => 45, 'fat' => 18,
        'ingredients' => "150g Salmon\nQuinoa\nAvocado\nSpinach",
        'steps'       => "Season salmon.\nPan-sear salmon.\nAssemble bowl.",
    ],
    [
        'title'       => 'Quinoa Power Salad',
        'category'    => 'lunch',
        'description' => 'Plant-based protein powerhouse with a light lemon dressing.',
        'image'       => 'salad.avif',
        'calories'    => 380, 'protein' => 18, 'carbs' => 52, 'fat' => 12,
        'ingredients' => "Quinoa\nChickpeas\nTomatoes\nCucumber",
        'steps'       => "Cook quinoa.\nMix ingredients.\nToss with dressing.",
    ],
    [
        'title'       => 'Pro-Dough Pizza',
        'category'    => 'dinner',
        'description' => 'Cheat meal macros without the cheat meal guilt.',
        'image'       => 'pizza.avif',
        'calories'    => 640, 'protein' => 55, 'carbs' => 48, 'fat' => 22,
        'ingredients' => "Protein Crust\nTomato Sauce\nMozzarella\nGrilled Chicken",
        'steps'       => "Preheat oven.\nTop pizza.\nBake for 12 mins.",
    ],
    [
        'title'       => 'Lemon Garlic Chicken',
        'category'    => 'lunch',
        'description' => 'Classic lean protein prepped in under 20 minutes.',
        'image'       => 'garlic chircken.avif',
        'calories'    => 410, 'protein' => 48, 'carbs' => 4, 'fat' => 12,
        'ingredients' => "Chicken Breast\nGarlic\nLemon\nOlive Oil",
        'steps'       => "Marinate chicken.\nSear in pan.\nLet rest.",
    ],
    [
        'title'       => 'Beef & Broccoli Stir Fry',
        'category'    => 'dinner',
        'description' => 'Iron-rich dinner to support muscle recovery.',
        'image'       => 'stir fry.avif',
        'calories'    => 480, 'protein' => 45, 'carbs' => 15, 'fat' => 18,
        'ingredients' => "Lean Beef\nBroccoli\nSoy Sauce\nGinger",
        'steps'       => "Sear beef.\nSteam broccoli.\nToss together.",
    ],
    [
        'title'       => 'Overnight Protein Oats',
        'category'    => 'breakfast',
        'description' => 'The ultimate convenient high-protein breakfast.',
        'image'       => 'salad2.avif',
        'calories'    => 420, 'protein' => 32, 'carbs' => 48, 'fat' => 10,
        'ingredients' => "Rolled Oats\nWhey Protein\nAlmond Milk\nBerries",
        'steps'       => "Mix oats and protein.\nAdd milk.\nRefrigerate overnight.",
    ],

    // Filipino Healthy Selection
    [
        'title'       => 'Lean Chicken Adobo',
        'category'    => 'dinner',
        'description' => 'A healthy take on the Filipino classic using skinless chicken cuts.',
        'image'       => 'adobo.avif',
        'calories'    => 350, 'protein' => 40, 'carbs' => 8, 'fat' => 15,
        'ingredients' => "Skinless Chicken\nSoy Sauce\nVinegar\nGarlic",
        'steps'       => "Simmer chicken in soy-vinegar mix.\nAdd garlic and bay leaves.\nCook until tender.",
    ],
    [
        'title'       => 'Beef Tapa Power Bowl',
        'category'    => 'breakfast',
        'description' => 'Thinly sliced lean beef cured in calamansi and garlic.',
        'image'       => 'tapa.avif',
        'calories'    => 450, 'protein' => 42, 'carbs' => 35, 'fat' => 14,
        'ingredients' => "Lean Beef\nCalamansi\nGarlic\nBrown Rice",
        'steps'       => "Marinate beef slices.\nPan-fry until browned.\nServe with egg and rice.",
    ],
    [
        'title'       => 'Bangus Steak',
        'category'    => 'lunch',
        'description' => 'Heart-healthy milkfish pan-seared with onions and soy sauce.',
        'image'       => 'milkfish.jpg',
        'calories'    => 320, 'protein' => 35, 'carbs' => 10, 'fat' => 12,
        'ingredients' => "Bangus Fillet\nOnions\nSoy Sauce\nLemon",
        'steps'       => "Sear fish.\nSauté onions.\nSimmer with sauce.",
    ],
    [
        'title'       => 'Chicken Tinola',
        'category'    => 'dinner',
        'description' => 'Warm, comforting ginger soup with lean chicken and papaya.',
        'image'       => 'tinoloa.jpg',
        'calories'    => 280, 'protein' => 38, 'carbs' => 12, 'fat' => 8,
        'ingredients' => "Chicken\nGinger\nPapaya\nSpinach",
        'steps'       => "Sauté ginger and chicken.\nAdd water and simmer.\nAdd papaya and greens.",
    ],
    [
        'title'       => 'Shrimp & Tofu Pinakbet',
        'category'    => 'lunch',
        'description' => 'Filipino vegetable medley powered with extra tofu and shrimp.',
        'image'       => 'tofu.jpg',
        'calories'    => 310, 'protein' => 25, 'carbs' => 28, 'fat' => 9,
        'ingredients' => "Tofu\nShrimp\nEggplant\nOkra",
        'steps'       => "Pan-fry tofu blocks.\nSauté shrimp and veggies.\nCombine with light sauce.",
    ],
    [
        'title'       => 'Grilled Stuffed Squid',
        'category'    => 'dinner',
        'description' => 'High-protein squid stuffed with fresh tomatoes and onions.',
        'image'       => 'squid.jpg',
        'calories'    => 290, 'protein' => 36, 'carbs' => 10, 'fat' => 6,
        'ingredients' => "Fresh Squid\nTomatoes\nOnions\nGinger",
        'steps'       => "Stuff squid cavity.\nGrill until tender.\nServe with vinegar dip.",
    ],
    [
        'title'       => 'Lean Pork Sinigang',
        'category'    => 'dinner',
        'description' => 'Sour tamarind soup with lean pork tenderloin and greens.',
        'image'       => 'sinigang.jpg',
        'calories'    => 340, 'protein' => 35, 'carbs' => 15, 'fat' => 12,
        'ingredients' => "Pork Tenderloin\nTamarind Base\nRadish\nKangkong",
        'steps'       => "Boil pork until tender.\nAdd tamarind and veggies.\nSimmer until sour.",
    ],
    [
        'title'       => 'Chicken Tortang Talong',
        'category'    => 'breakfast',
        'description' => 'Smoky grilled eggplant omelet with ground lean chicken.',
        'image'       => 'talong.jpg',
        'calories'    => 310, 'protein' => 28, 'carbs' => 8, 'fat' => 16,
        'ingredients' => "Eggplant\nGround Chicken\nEggs\nGarlic",
        'steps'       => "Grill eggplant.\nMix with eggs and chicken.\nPan-fry like an omelet.",
    ],
    [
        'title'       => 'Tokwa\'t Baboy (Lean)',
        'category'    => 'snacks',
        'description' => 'Fried tofu and boiled lean pork in a savory soy-vinegar sauce.',
        'image'       => 'tokwatbaboy.jpg',
        'calories'    => 280, 'protein' => 30, 'carbs' => 10, 'fat' => 12,
        'ingredients' => "Tofu\nLean Pork\nSoy Sauce\nVinegar",
        'steps'       => "Fry tofu.\nBoil pork and slice.\nMix with soy-vinegar sauce.",
    ],
    [
        'title'       => 'Brown Rice Arroz Caldo',
        'category'    => 'breakfast',
        'description' => 'Wholesome ginger rice porridge with extra chicken breast.',
        'image'       => 'arozcaldo.jpg',
        'calories'    => 380, 'protein' => 32, 'carbs' => 45, 'fat' => 7,
        'ingredients' => "Brown Rice\nChicken Breast\nGinger\nGarlic",
        'steps'       => "Sauté ginger.\nCook rice and chicken into porridge.\nTop with garlic.",
    ],
    [
        'title'       => 'Lean Bicol Express',
        'category'    => 'dinner',
        'description' => 'Spicy coconut stew using lean pork and light coconut milk.',
        'image'       => 'bicol.jpg',
        'calories'    => 390, 'protein' => 34, 'carbs' => 12, 'fat' => 22,
        'ingredients' => "Lean Pork\nChili\nLight Coconut Milk\nBeans",
        'steps'       => "Sauté pork and chili.\nSimmer in light coconut milk.\nReduce sauce until thick.",
    ],
    [
        'title'       => 'Mung Bean & Shrimp Soup',
        'category'    => 'lunch',
        'description' => 'Nutrient-rich mung bean stew with fresh shrimp and spinach.',
        'image'       => 'mungbean.jpg',
        'calories'    => 320, 'protein' => 24, 'carbs' => 38, 'fat' => 6,
        'ingredients' => "Mung Beans\nShrimp\nSpinach\nGarlic",
        'steps'       => "Boil beans until soft.\nSauté garlic and shrimp.\nCombine and simmer.",
    ],
    [
        'title'       => 'Grilled Chicken Inasal',
        'category'    => 'dinner',
        'description' => 'Bacolod-style grilled chicken marinated in lemongrass and ginger.',
        'image'       => 'inasal.jpg',
        'calories'    => 360, 'protein' => 45, 'carbs' => 2, 'fat' => 18,
        'ingredients' => "Chicken Legs\nLemongrass\nAchiote\nGinger",
        'steps'       => "Marinate chicken.\nGrill over charcoal.\nBaste with achiote oil.",
    ],
    [
        'title'       => 'Fresh Lumpiang Sariwa',
        'category'    => 'snacks',
        'description' => 'Fresh vegetable spring rolls with a sweet peanut-garlic sauce.',
        'image'       => 'sariwa.jpg',
        'calories'    => 260, 'protein' => 15, 'carbs' => 32, 'fat' => 8,
        'ingredients' => "Cabbage\nCarrots\nTofu\nPeanut Sauce",
        'steps'       => "Sauté veggies and tofu.\nWrap in fresh crepe.\nTop with peanut sauce.",
    ],
    [
        'title'       => 'Tilapia with Mango Salsa',
        'category'    => 'lunch',
        'description' => 'Pan-seared tilapia topped with a fresh and zesty mango salsa.',
        'image'       => 'mangosalsa.jpg',
        'calories'    => 290, 'protein' => 34, 'carbs' => 18, 'fat' => 8,
        'ingredients' => "Tilapia Fillet\nMango\nRed Onion\nLime",
        'steps'       => "Sear tilapia.\nDice mango and onions.\nTop fish with salsa.",
    ],

    // Healthy Snacks
    [
        'title'       => 'High-Protein Banana Muffins',
        'category'    => 'snacks',
        'description' => 'Fluffy, refined sugar-free muffins packed with whey protein and oats.',
        'image'       => 'banana.jpg',
        'calories'    => 180, 'protein' => 12, 'carbs' => 22, 'fat' => 4,
        'ingredients' => "Ripe Bananas\nOat Flour\nVanilla Whey\nEgg Whites",
        'steps'       => "Mash bananas.\nMix in dry ingredients.\nBake at 350°F for 15 mins.",
    ],
    [
        'title'       => 'Baked Kamote Chips',
        'category'    => 'snacks',
        'description' => 'Thinly sliced sweet potatoes baked until crisp with sea salt.',
        'image'       => 'kamote.jpg',
        'calories'    => 150, 'protein' => 2, 'carbs' => 34, 'fat' => 1,
        'ingredients' => "Orange Kamote\nOlive Oil Spray\nSea Salt\nPaprika",
        'steps'       => "Slice kamote very thin.\nToss with salt and oil.\nBake until crispy.",
    ],
    [
        'title'       => 'Tuna Stuffed Peppers',
        'category'    => 'snacks',
        'description' => 'Mini bell peppers filled with high-protein tuna and Greek yogurt.',
        'image'       => 'tuna.jpg',
        'calories'    => 140, 'protein' => 22, 'carbs' => 6, 'fat' => 2,
        'ingredients' => "Mini Bell Peppers\nCanned Tuna\nGreek Yogurt\nDill",
        'steps'       => "Mix tuna and yogurt.\nStuff into pepper halves.\nTop with fresh dill.",
    ],
    [
        'title'       => 'Mango Greek Yogurt Parfait',
        'category'    => 'snacks',
        'description' => 'Creamy Greek yogurt layered with sweet Philippine mangoes and nuts.',
        'image'       => 'yogurt.jpg',
        'calories'    => 210, 'protein' => 18, 'carbs' => 25, 'fat' => 5,
        'ingredients' => "Philippine Mango\nNon-fat Greek Yogurt\nAlmonds\nHoney",
        'steps'       => "Layer yogurt in a glass.\nAdd diced mangoes.\nTop with crushed almonds.",
    ],
];

$inserted = 0;
$skipped  = 0;

// Prepared statement 
$sql = 'INSERT IGNORE INTO recipes (title, category, description, image, calories, protein, carbs, fat, ingredients, steps)
        VALUES (:title, :category, :description, :image, :calories, :protein, :carbs, :fat, :ingredients, :steps)';
$stmt = $pdo->prepare($sql);

foreach ($recipes as $r) {
    $stmt->execute([
        ':title'       => $r['title'],
        ':category'    => $r['category'],
        ':description' => $r['description'],
        ':image'       => $r['image'],
        ':calories'    => $r['calories'],
        ':protein'     => $r['protein'],
        ':carbs'       => $r['carbs'],
        ':fat'         => $r['fat'],
        ':ingredients' => $r['ingredients'],
        ':steps'       => $r['steps'],
    ]);

    if ($stmt->rowCount() > 0) {
        $inserted++;
    } else {
        $skipped++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seed Recipes — AthleticEats</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/global.css">
    <style>
        .result-wrap {
            max-width: 500px;
            margin: 5rem auto;
            padding: 0 1.5rem;
            text-align: center;
        }
        .result-card {
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 3rem 2.5rem;
        }
        .icon { font-size: 3rem; margin-bottom: 1rem; }
        h1 { font-size: 1.5rem; font-weight: 800; margin-bottom: 0.75rem; }
        h1 span { color: var(--accent-coral); }
        .stats { margin: 1.5rem 0; display: flex; justify-content: center; gap: 2rem; }
        .stat-box { background: var(--bg-color); border-radius: var(--radius-sm); padding: 1rem 1.5rem; }
        .stat-box .num { font-size: 2rem; font-weight: 800; color: var(--accent-coral); }
        .stat-box .lbl { font-size: 0.82rem; color: var(--text-muted); }
        p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.5rem; }
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

<div class="result-wrap">
    <div class="result-card">
        <div class="icon">&#9989;</div>
        <h1>Seed <span>Complete</span></h1>

        <div class="stats">
            <div class="stat-box">
                <div class="num"><?= $inserted ?></div>
                <div class="lbl">Recipes Added</div>
            </div>
            <div class="stat-box">
                <div class="num"><?= $skipped ?></div>
                <div class="lbl">Already Existed</div>
            </div>
        </div>

        <p>All existing AthleticEats recipes are now in the database.<br>You can safely run this page again — duplicates are skipped automatically.</p>
        <a href="index.php" class="btn-primary">Go to Recipe Manager</a>
    </div>
</div>

<footer><p>&copy; <?= date('Y') ?> AthleticEats. All rights reserved.</p></footer>
</body>
</html>
