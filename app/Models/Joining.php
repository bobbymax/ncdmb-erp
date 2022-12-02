<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joining extends Model
{
    use HasFactory;

    protected $guarded = [''];

    protected $dates = ['start', 'end'];

    public function training(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Training::class, 'training_id');
    }

    public function staff(): \Illuminate\Database\Eloquent\Relations\MorphToMany
    {
        return $this->morphToMany(User::class, 'userable');
    }

    public function qualification(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Qualification::class, 'qualification_id');
    }

    public function addParticipant(User $user)
    {
        return $this->staff()->save($user);
    }

    public function timeline(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function learningCategory(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LearningCategory::class, 'learning_category_id');
    }
}
