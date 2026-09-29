<?php
use App\Models\Student;

require_once __DIR__ . '/includes/helpers.php';

$pageTitle   = 'Welcome';
$currentPage = 'index.php';

$total   = Student::count();
$courses = Student::countDistinct('course');
$latest  = Student::latest()->first();

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <h1>Every student, on one register.</h1>
    <p class="lead">
        Record new students as they enrol and look anyone up in seconds.
        Names, student numbers, courses and year of study, all in one place.
    </p>
    <div class="actions">
        <a class="button" href="add.php">Add a student</a>
        <a class="button button-quiet" href="students.php">View records</a>
    </div>
</section>

<section class="tally" aria-label="Register summary">
    <p class="tally-count"><?= $total ?></p>
    <p class="tally-text">
        <?php if ($total === 0): ?>
            No students recorded yet. <a href="add.php">Add the first one</a>.
        <?php else: ?>
            <?= $total === 1 ? 'student' : 'students' ?> recorded across
            <?= $courses ?> <?= $courses === 1 ? 'course' : 'courses' ?>.
            Most recently added: <strong><?= e($latest->full_name) ?></strong>,
            <?= e($latest->course) ?>.
        <?php endif; ?>
    </p>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
