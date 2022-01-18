<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ProjectMember;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'invite_link_token',
        'invite_link_status',
    ];

    protected $casts = [
        'invite_link_status' => 'boolean',
        'created_at'    => 'date',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d-m-Y');
    }
    // ------------------------------------------------------------------------- attributes
    public function getInviteLinkAttribute()
    {
        return ($this->invite_link_token) ? route('project.invite-link', $this->invite_link_token) : null;
    }
    // ------------------------------------------------------------------------- relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function joinedMembers()
    {
        return $this->hasMany(ProjectMember::class)->where('validated_at', '<>', null);
    }

    public function taskLists()
    {
        return $this->hasMany(TaskList::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, TaskList::class);
    }

    public function tasksCompleted()
    {
        return $this->hasManyThrough(Task::class, TaskList::class)->where('completed', true);
    }
    // ------------------------------------------------------------------------- scope
    public function scopeApplyFilters($query, array $filters)
    {
        $filters = collect($filters);

        if ($filters->get('search')) {
            $search = $filters->get('search');
            $query->where('title', 'LIKE', '%'.$search.'%');
        }
    }
}
