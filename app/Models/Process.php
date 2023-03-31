<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function stages(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Stage::class);
    }

    public function roles(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Role::class, Stage::class);
    }

    public function nextStage(Stage $stage)
    {
        $order = $stage->order + 1;
        return $this->stages->where('order', $order)->first() ?? null;
    }
}
