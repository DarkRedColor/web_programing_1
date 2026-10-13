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
