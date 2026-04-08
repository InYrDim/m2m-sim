<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timeslot extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'time_start',
        'time_end',
        'full_time'
    ];

    public function days()
    {
        return $this->belongsToMany(Day::class, 'day_time')->withTimestamps();
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
