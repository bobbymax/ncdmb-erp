<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function joinings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Joining::class);
    }
}
