<?php
namespace App\Models;

use App\Database\Model;

class Joke extends Model
{
    protected static string $table = 'joke';

    protected static array $fillable = [
        'joketext',
        'jokedate',
    ];

    /** Date the joke was added, e.g. "13 Oct 2026". */
    public function postedOn(): string
    {
        return date('j M Y', strtotime($this->jokedate));
    }
}
