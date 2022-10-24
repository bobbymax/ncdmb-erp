<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function timeline(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }
}
