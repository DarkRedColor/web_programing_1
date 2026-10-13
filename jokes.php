<?php
use App\Models\Joke;

require_once __DIR__ . '/includes/helpers.php';

$pageTitle   = 'Jokes';
$currentPage = 'jokes.php';

// Newest first; jokes from the same day are ordered by id.
$jokes = Joke::orderBy('jokedate', 'desc')->orderBy('id', 'desc')->get();

require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Jokes</h1>
<p class="page-intro">
    <?= count($jokes) ?> <?= count($jokes) === 1 ? 'joke' : 'jokes' ?> on the register, newest first.
</p>

<?php if (!$jokes): ?>
    <div class="empty">
        <p>No jokes yet. Add some rows to the <code>joke</code> table and they'll show up here.</p>
    </div>
<?php else: ?>
    <ol class="jokes">
        <?php foreach ($jokes as $joke): ?>
            <li class="joke">
                <blockquote>
                    <p><?= nl2br(e($joke->joketext)) ?></p>
                </blockquote>
                <p class="joke-meta">
                    <span class="joke-number">#<?= (int) $joke->id ?></span>
                    <time datetime="<?= e($joke->jokedate) ?>"><?= e($joke->postedOn()) ?></time>
                </p>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
