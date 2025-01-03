<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomTeacher extends Model
{
    protected $table = 'classrooms_teachers';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id', 'id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
