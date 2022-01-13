<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    const CREATED_AT = 'assigned_at';

    const PRIORITY_LOW = 'Low';
    const PRIORITY_NORMAL = 'Normal';
    const PRIORITY_HIGH = 'High';

    public static $priorities = [
        self::PRIORITY_LOW,
        self::PRIORITY_NORMAL,
        self::PRIORITY_HIGH,
    ];

    protected $fillable = [
        'project_id',
        'member_id',
        'title',
        'description',
        'priority',
        'start_at',
        'due_at',
        'completed',
    ];

    protected $casts = [
        'completed'     => 'boolean',
        'start_at'      => 'date',
        'due_at'        => 'date',
        'assigned_at'   => 'date',
        'updated_at'    => 'date',
    ];

    protected $touches = ['taskList'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d-m-Y');
    }

    // ------------------------------------------------------------------------- relationship
    // public function project()
    // {
    //     return $this->belongsTo(Project::class);
    // }

    public function taskList()
    {
        return $this->belongsTo(TaskList::class);
    }

    public function arranger() // person assign this task
    {
        return $this->belongsTo(ProjectMember::class, 'member_id');
    }

    public function performers() // assign to
    {
        return $this->belongsToMany(
            ProjectMember::class,
            'task_members',
            'task_id',
            'member_id',
        )
        ->using(TaskMember::class)
        ->withPivot('request_complete');
    }

    public function performers_request_complete() // assign to
    {
        return $this->belongsToMany(
            ProjectMember::class,
            'task_members',
            'task_id',
            'member_id',
        )
        ->using(TaskMember::class)
        ->withPivot('request_complete')
        ->wherePivot('request_complete', true);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->withTrashed();
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }
}
