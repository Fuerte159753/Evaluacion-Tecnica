<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
        // modelo de la tabla task
    protected $table = 'tasks';
    protected $fillable = [
        'id',
        'title',
        'description',
        'status',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
    public function persons()
    {
        return $this->belongsToMany(Person::class, 'person_task', 'task_id', 'persons_id');
    }
}
