<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'member_id',
        'task_id',
        'content',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d-m-Y');
    }

    // ------------------------------------------------------------------------- relationship
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function member()
    {
        return $this->belongsTo(ProjectMember::class);
    }

}
