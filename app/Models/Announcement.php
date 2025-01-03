<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Announcement extends Model
{   
    use SoftDeletes;
    
    protected $table = 'announcements';

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function creator() {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
