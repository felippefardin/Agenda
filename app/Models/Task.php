<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    
    protected $fillable = [
        'title', 
        'description', 
        'category', 
        'priority', 
        'start_time', 
        'end_time', 
        'is_notified'
    ];

    
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_notified' => 'boolean'
    ];
}