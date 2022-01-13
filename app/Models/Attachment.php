<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'link',
        'type',
    ];

    public function getLinkAttribute($value)
    {
        return ($this->type != "url") ? Storage::disk(config('public'))->url($value) : $value;
    }

    // ------------------------------------------------------------------------- relationship
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
