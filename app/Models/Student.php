<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'name',
        'email',
        'course',
        'year',
        'gender',
        'assignment_score',
        'midterm_score',
        'attendance_rate',
        'risk_level', 
        'photo'
    ];
}
