<?php
// Expects $pageTitle and $currentPage to be set by the page including this file.
$nav = [
    'index.php'    => 'Home',
    'add.php'      => 'Add student',
    'students.php' => 'Student records',
    'calculator.php' => 'Calculator',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | Student Register</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="site-header">
    <a href="index.php" class="brand">Student Register</a>
    <nav>
        <?php foreach ($nav as $file => $label): ?>
            <a href="<?= $file ?>" <?= $currentPage === $file ? 'aria-current="page"' : '' ?>><?= $label ?></a>
        <?php endforeach; ?>
    </nav>
</header>
<main>
<?php if ($msg = take_flash()): ?>
    <p class="flash" role="status"><?= e($msg) ?></p>
<?php endif; ?>
