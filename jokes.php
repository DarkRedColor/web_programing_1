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
<div class="two-column">
    <p class="page-intro">
        <?= count($jokes) ?> <?= count($jokes) === 1 ? 'joke' : 'jokes' ?> on the register, newest first.
    </p>
    <a class="button" href='./jokesAdd.php'">Add a joke</a>
</div>


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
                    <time datetime="<?= e($joke->jokedate) ?>"><?= e($joke->addedOn()) ?></time>
                    <button class="button joke-delete" data-id="<?= (int) $joke->id ?>">Delete</button>
                </p>
            </li>
        <?php endforeach; ?>
    </ol>
<?php endif; ?>

<script>
    const csrf = <?= json_encode(csrf_token()) ?>;

    document.querySelectorAll('.joke-delete').forEach(button => {
        button.addEventListener('click', async () => {
            if (!confirm('Delete this joke?')) return;

            const body = new FormData();
            body.append('id', button.dataset.id);
            body.append('csrf', csrf);

            const response = await fetch('jokeDelete.php', { method: 'POST', body });

            if (response.ok) {
                button.closest('.joke').remove();   // remove the card from the page
            } else {
                alert('Could not delete the joke. Try reloading the page.');
            }
        });
    });
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
