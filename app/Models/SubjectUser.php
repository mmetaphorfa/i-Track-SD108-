<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectUser extends Model
{
    protected $table = 'subjects_users';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public $timestamps = false;

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
