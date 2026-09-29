<?php
use App\Models\Student;

require_once __DIR__ . '/includes/helpers.php';

$pageTitle   = 'Student records';
$currentPage = 'students.php';

// --- Delete a record (POST only, never via a plain link) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    csrf_check();

    $student = Student::find((int) $_POST['delete_id']);

    if ($student) {
        $student->delete();
        flash($student->full_name . ' removed from the register.');
    }
    redirect('students.php');
}

// --- Search ---
$search = trim($_GET['q'] ?? '');

$students = $search !== ''
    ? Student::search($search)
    : Student::orderBy('full_name')->get();

require __DIR__ . '/includes/header.php';
?>

<div class="table-head">
    <div>
        <h1 class="page-title">Student records</h1>
        <p class="page-intro">
            <?= count($students) ?> <?= count($students) === 1 ? 'record' : 'records' ?>
            <?= $search !== '' ? 'matching "' . e($search) . '"' : '' ?>
        </p>
    </div>
    <a class="button" href="add.php">Add student</a>
</div>

<form method="get" class="search" role="search">
    <label for="q" class="visually-hidden">Search students</label>
    <input type="search" id="q" name="q" value="<?= e($search) ?>" placeholder="Search by name, number, email or course">
    <button type="submit" class="button button-quiet">Search</button>
    <?php if ($search !== ''): ?>
        <a href="students.php" class="clear">Clear</a>
    <?php endif; ?>
</form>

<?php if (!$students): ?>
    <div class="empty">
        <?php if ($search !== ''): ?>
            <p>No students match "<?= e($search) ?>". Check the spelling or <a href="students.php">show all records</a>.</p>
        <?php else: ?>
            <p>The register is empty. <a href="add.php">Add the first student</a> to see them here.</p>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th scope="col">Student no.</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Course</th>
                    <th scope="col" class="num">Year</th>
                    <th scope="col">Added</th>
                    <th scope="col"><span class="visually-hidden">Actions</span></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td class="code"><?= e($s->student_number) ?></td>
                        <td><?= e($s->full_name) ?></td>
                        <td><a href="mailto:<?= e($s->email) ?>"><?= e($s->email) ?></a></td>
                        <td><?= e($s->course) ?></td>
                        <td class="num"><?= (int) $s->year_of_study ?></td>
                        <td><?= e($s->addedOn()) ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Remove <?= e(addslashes($s->full_name)) ?> from the register?');">
                                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="delete_id" value="<?= (int) $s->id ?>">
                                <button type="submit" class="link-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
