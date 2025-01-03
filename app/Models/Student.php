<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $table = 'students';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function parent() {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }

    public function enrollments() {
        return $this->belongsToMany(Classroom::class, 'classrooms_students', 'student_id', 'class_id');
    }

    public function attendance() {
        return $this->hasMany(ClassroomStudent::class, 'student_id', 'id');
    }

    public function classrooms() {
        return $this->belongsToMany(Classroom::class, 'classrooms_students', 'student_id', 'class_id');
    }
}
