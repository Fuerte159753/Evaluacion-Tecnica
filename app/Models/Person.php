<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = [
        'id',
        'name',
        'avatar',
    ];

    public function setAvatarAttribute($value)
    {
        $this->attributes['avatar'] = base64_encode($value);
    }

    public function getAvatarAttribute($value)
    {
        return base64_decode($value);
    }
        public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return 'data:image/png;base64,' . $this->avatar;
        }
        return null;
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'person_task', 'persons_id', 'task_id');
    }
}
