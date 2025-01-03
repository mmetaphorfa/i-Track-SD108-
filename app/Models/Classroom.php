<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $table = 'classrooms';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teachers() {
        return $this->belongsToMany(User::class, 'classrooms_users')->withPivot('assigned_at', 'end_at', 'status');
    }

    public function students() {
        return $this->belongsToMany(Student::class, 'classrooms_students', 'class_id', 'student_id')->withPivot('enrolled_at', 'status');
    }

    public function timetables() {
        return $this->hasMany(Timetable::class, 'class_id', 'id');
    }
}
