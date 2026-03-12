<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    
protected $fillable = [
    'user_id', 
    'title',
    'description',
    'priority',
    'status',      
    'start_time',
    'end_time',
];
    
    protected $casts = [
    'start_time' => 'datetime',
    'end_time' => 'datetime',
    'is_notified' => 'boolean'
];
}