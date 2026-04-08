<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function timeslots()
    {
        return $this->belongsToMany(Timeslot::class, 'day_time')->withTimestamps();
    }

    public function timetables()
    {
        return $this->hasMany(Timetable::class);
    }
}
