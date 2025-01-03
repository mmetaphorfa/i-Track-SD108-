<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassroomUser extends Model
{
    protected $table = 'classrooms_users';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public $timestamps = false;

    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }
}
