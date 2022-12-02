<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function trainings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Joining::class);
    }
}
