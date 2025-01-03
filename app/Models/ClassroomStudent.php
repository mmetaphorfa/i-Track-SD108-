<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomStudent extends Model
{
    protected $table = 'classrooms_students';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public $timestamps = false;

    public function student() {
        return $this->belongsTo(Student::class);
    }
}
