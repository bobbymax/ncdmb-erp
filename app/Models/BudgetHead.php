<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetHead extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function subBudgetHeads(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubBudgetHead::class);
    }

    public function funds(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(Fund::class, SubBudgetHead::class);
    }

    public function fund(): \Illuminate\Database\Eloquent\Relations\HasOneThrough
    {
        return $this->hasOneThrough(Fund::class, SubBudgetHead::class)->where('year', 2022);
    }
}
