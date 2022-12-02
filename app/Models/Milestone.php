<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Milestone extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function milestoneable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function timeline(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }
}
