<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $table = 'subjects';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function teachers() {
        return $this->belongsToMany(User::class, 'subjects_users');
    }
}
