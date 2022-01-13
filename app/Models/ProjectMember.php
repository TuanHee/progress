<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'email',
        'invite_token',
        'is_admin',
        'validated_at',
    ];

    protected $casts = [
        'is_admin'      => 'boolean',
        'validated_at'  => 'date',
    ];

    protected $appends = [
        'name',
        'profile_photo_url',
    ];

    protected $touches = ['project'];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('d-m-Y');
    }

    // ------------------------------------------------------------------------- attributes
    public function getNameAttribute()
    {
        return $this->user->name ?? substr($this->email, 0, strrpos($this->email, '@'));
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->user->id ? $this->user->profile_photo_url : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    // ------------------------------------------------------------------------- relationship
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name'  => null,
            'profile_photo_url' => null,
        ]);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
