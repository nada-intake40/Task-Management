<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['title' , 'description' , 'due_date' , 'status' , 'employee_id' , "parent_task_id"];

    protected $casts = [
        'due_date' => 'datetime:Y-m-d H:i',
        "created_at" => 'datetime:Y-m-d H:i',
        "updated_at" => 'datetime:Y-m-d H:i',
    ];

    public function employee()
    {
        return $this->belongsTo(User::class , "employee_id");
    }

    public function taskDependencies()
    {
        return $this->hasMany(Task::class , "parent_task_id");
    }

}
