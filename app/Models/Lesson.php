<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
