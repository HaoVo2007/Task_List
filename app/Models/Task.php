<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{   
    protected $fillable = [
        'task',
        'category',
        'priority',
        'deadline',
        'reminder',
        'check_notification',
    ];
    
    use HasFactory;
}
