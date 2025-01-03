<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function class() {
        return $this->belongsTo(Classroom::class, 'class_id', 'id');
    }
}
