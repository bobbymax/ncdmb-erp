<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeLevel extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function records(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Record::class);
    }

    public function settlements(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Settlement::class);
    }
}
