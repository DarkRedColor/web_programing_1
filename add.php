<?php
use App\Models\Student;

require_once __DIR__ . '/includes/helpers.php';

$pageTitle   = 'Add student';
$currentPage = 'add.php';

// Values shown in the form (kept after a failed submit so nothing is lost).
$old = [
    'full_name'      => '',
    'student_number' => '',
    'email'          => '',
    'course'         => '',
    'year_of_study'  => '',
];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();

    foreach ($old as $field => $_) {
        $old[$field] = trim($_POST[$field] ?? '');
    }

    // --- Validation ---
    if ($old['full_name'] === '') {
        $errors['full_name'] = 'Enter the student\'s full name.';
    } elseif (mb_strlen($old['full_name']) > 100) {
        $errors['full_name'] = 'Keep the name under 100 characters.';
    }

    if (!preg_match('/^[A-Za-z0-9]{5,12}$/', $old['student_number'])) {
        $errors['student_number'] = 'Use 5 to 12 letters or numbers, e.g. S1234567.';
    }

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address, e.g. name@example.com.';
    }

    if (!in_array($old['course'], Student::COURSES, true)) {
        $errors['course'] = 'Choose a course from the list.';
    }

    if (!in_array($old['year_of_study'], ['1', '2', '3', '4'], true)) {
        $errors['year_of_study'] = 'Choose a year between 1 and 4.';
    }

    // --- Save ---
    if (!$errors) {
        $old['student_number'] = strtoupper($old['student_number']);

        if (Student::where('student_number', $old['student_number'])->exists()) {
            $errors['student_number'] = 'This student number is already on the register.';
        } else {
            // Only the $fillable columns are saved; values go through prepared statements.
            $student = Student::create($old);

            flash($student->full_name . ' added to the register.');
            redirect('students.php');
        }
    }
}

require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Add a student</h1>
<p class="page-intro">All fields are required.</p>

<form method="post" class="form" novalidate>
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="field <?= isset($errors['full_name']) ? 'has-error' : '' ?>">
        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" autocomplete="name">
        <?php if (isset($errors['full_name'])): ?><p class="error"><?= e($errors['full_name']) ?></p><?php endif; ?>
    </div>

    <div class="field-row">
        <div class="field <?= isset($errors['student_number']) ? 'has-error' : '' ?>">
            <label for="student_number">Student number</label>
            <input type="text" id="student_number" name="student_number" value="<?= e($old['student_number']) ?>" placeholder="S1234567">
            <?php if (isset($errors['student_number'])): ?><p class="error"><?= e($errors['student_number']) ?></p><?php endif; ?>
        </div>

        <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" autocomplete="email">
            <?php if (isset($errors['email'])): ?><p class="error"><?= e($errors['email']) ?></p><?php endif; ?>
        </div>
    </div>

    <div class="field-row">
        <div class="field <?= isset($errors['course']) ? 'has-error' : '' ?>">
            <label for="course">Course</label>
            <select id="course" name="course">
                <option value="">Choose a course</option>
                <?php foreach (Student::COURSES as $course): ?>
                    <option value="<?= e($course) ?>" <?= $old['course'] === $course ? 'selected' : '' ?>><?= e($course) ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['course'])): ?><p class="error"><?= e($errors['course']) ?></p><?php endif; ?>
        </div>

        <fieldset class="field <?= isset($errors['year_of_study']) ? 'has-error' : '' ?>">
            <legend>Year of study</legend>
            <div class="years">
                <?php foreach (['1', '2', '3', '4'] as $year): ?>
                    <label class="year">
                        <input type="radio" name="year_of_study" value="<?= $year ?>" <?= $old['year_of_study'] === $year ? 'checked' : '' ?>>
                        <span><?= $year ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (isset($errors['year_of_study'])): ?><p class="error"><?= e($errors['year_of_study']) ?></p><?php endif; ?>
        </fieldset>
    </div>

    <div class="actions">
        <button type="submit" class="button">Add student</button>
        <a href="students.php" class="button button-quiet">Cancel</a>
    </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
