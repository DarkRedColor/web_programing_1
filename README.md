# Student Register (PHP)

A small PHP site for recording student data:

- `index.php` – welcome page with a live count of recorded students
- `add.php` – form to add a student (with validation)
- `students.php` – table of all records, with search and remove
- `jokes.php` – lists every row of the `joke` table, newest first

Data is stored in MySQL. On first run the site creates the `student_register` database and the `students` table by itself. It connects as `root` with no password; change this in `config/db.php` if needed.

## Run it

**Option 1 – PHP's built-in server** (PHP 8+):

    cd student-records
    php -S localhost:8000

Then open http://localhost:8000

**Option 2 – XAMPP / WAMP / MAMP:** copy the `student-records` folder into `htdocs` (or `www`) and open http://localhost/student-records

Make sure MySQL is running first. If you see "could not find driver", enable `extension=pdo_mysql` in `php.ini` and restart.

## Project structure

    student-records/
    ├── index.php            Welcome page
    ├── add.php              Add-student form
    ├── students.php         Records table
    ├── app/
    │   ├── Database/
    │   │   ├── Model.php        Base model (find, create, save, delete…)
    │   │   └── QueryBuilder.php Builds safe SQL from chained calls
    │   └── Models/
    │       ├── Student.php      The students table as a class
    │       └── Joke.php         The joke table as a class
    ├── config/db.php        MySQL settings, database/table creation
    ├── includes/
    │   ├── helpers.php      Escaping, CSRF token, flash messages
    │   ├── header.php       Shared page top + navigation
    │   └── footer.php       Shared page bottom
    └── assets/style.css     Styles

## The mini ORM

The pages don't write SQL. They use a small Eloquent-style ORM in `app/`:

```php
use App\Models\Student;

Student::all();                                   // every row, as Student objects
Student::find(3);                                 // one row by id, or null
Student::where('course', 'Psychology')->get();    // filtered
Student::where('year_of_study', '>=', 2)
       ->orderBy('full_name')->limit(10)->get();  // chained
Student::where('email', 'a@b.com')->exists();     // true / false
Student::count();                                 // number of rows
Student::latest()->first();                       // newest row

$s = Student::create(['full_name' => 'Ana Popescu', /* ... */]);  // INSERT
echo $s->full_name;                               // columns are properties
$s->course = 'Graphic Design';
$s->save();                                       // UPDATE
$s->delete();                                     // DELETE
```

To add another table, create a class in `app/Models/` that extends `Model`
and sets `$table` and `$fillable`. Everything above then works for it too.

## Security basics included

- Prepared statements for every query (SQL injection)
- `htmlspecialchars` on all output (XSS)
- CSRF token on every form
- Deletes only via POST
