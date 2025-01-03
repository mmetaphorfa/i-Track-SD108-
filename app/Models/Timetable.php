<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $table = 'timetables';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function classroom() {
        return $this->belongsTo(Classroom::class, 'class_id', 'id');
    }

    public function creator() {
        return $this->belongsTo(User::class, 'upload_by', 'id');
    }
}
