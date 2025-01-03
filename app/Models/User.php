<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $guarded = ['id', 'remember_token', 'created_at', 'updated_at'];

    protected $hidden = ['username', 'password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'full_name' => 'encrypted',
            'phone' => 'encrypted',
            'password' => 'hashed',
        ];
    }

    public function address() {
        return $this->hasOne(Address::class);
    }

    public function subjects() {
        return $this->belongsToMany(Subject::class, 'subjects_users');
    }

    public function classes() {
        return $this->belongsToMany(Classroom::class, 'classrooms_users');
    }

    public function payments() {
        return $this->hasMany(Payment::class);
    }
}
