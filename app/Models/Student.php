<?php
namespace App\Models;

use App\Database\Model;

class Student extends Model
{
    protected static string $table = 'students';

    protected static array $fillable = [
        'full_name',
        'student_number',
        'email',
        'course',
        'year_of_study',
    ];

    /** The list of courses shown in the form's dropdown. */
    public const COURSES = [
        'Computer Science',
        'Software Engineering',
        'Business Management',
        'Accounting and Finance',
        'Psychology',
        'Graphic Design',
    ];

    /** Students whose name, number, email or course contains $term. */
    public static function search(string $term): array
    {
        $like = '%' . $term . '%';

        return static::query()
            ->where('full_name', 'LIKE', $like)
            ->orWhere('student_number', 'LIKE', $like)
            ->orWhere('email', 'LIKE', $like)
            ->orWhere('course', 'LIKE', $like)
            ->orderBy('full_name')
            ->get();
    }

    /** Date the student was added, e.g. "29 Sep 2026". */
    public function addedOn(): string
    {
        return date('j M Y', strtotime($this->created_at));
    }
}
