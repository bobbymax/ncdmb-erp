<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function commitment(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Commitment::class, 'commitment_id');
    }

    public function milestones(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Milestone::class, 'milestoneable');
    }
}
