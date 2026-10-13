<?php

use App\Models\Joke;

require_once __DIR__ . '/includes/helpers.php';

$pageTitle = 'Add joke';
$currentPage = 'jokesAdd.php';

$old = [
        'joketext' => '',

];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    foreach ($old as $field => $_) {
        $old[$field] = trim($_POST[$field] ?? '');
    }

    // --- Validation ---
    if ($old['joketext'] === '') {
        $errors['joketext'] = 'Enter joke text.';
    } elseif (mb_strlen($old['joketext']) > 65500) {
        $errors['joketext'] = 'Keep the joke under 65500 characters, it is not a book.';
    }


    // --- Save ---
    if (!$errors) {

        // Only the $fillable columns are saved; values go through prepared statements.
        $old['jokedate'] = date('Y-m-d');
        $joke = Joke::create($old);

        redirect('jokes.php');

    }
}

require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Add a joke</h1>
<p class="page-intro">All fields are required.</p>

<form method="post" class="form" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="field <?= isset($errors['joketext']) ? 'has-error' : '' ?>">
        <label for="joketext">Full name</label>
        <input type="text" id="joketext" name="joketext" value="<?= e($old['joketext']) ?>">
        <?php if (isset($errors['joketext'])): ?><p class="error"><?= e($errors['joketext']) ?></p><?php endif; ?>
    </div>

    <div class="actions">
        <button type="submit" class="button">Add joke</button>
        <a href="jokes.php" class="button button-quiet">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
