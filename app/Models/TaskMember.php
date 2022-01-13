<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class TaskMember extends Pivot
{
    // use HasFactory;
    protected $casts = [
        'request_complete'  => 'boolean',
        'created_at'    => 'date',
        'updated_at'    => 'date',
    ];
}
