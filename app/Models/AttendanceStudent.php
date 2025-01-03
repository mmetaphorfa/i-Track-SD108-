<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStudent extends Model
{
    protected $table = 'attendances_students';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function student() {
        return $this->belongsTo(Student::class);
    }
}
