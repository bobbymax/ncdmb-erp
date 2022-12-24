<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pillar extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function responsibilities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Responsibility::class);
    }
}
