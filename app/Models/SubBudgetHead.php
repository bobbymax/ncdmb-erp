<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubBudgetHead extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function budgetHead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(BudgetHead::class, 'budget_head_id');
    }

    public function funds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Fund::class);
    }

    public function fund(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Fund::class)->where('year', $this->getBudgetYear());
    }

    public function expenditures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expenditure::class);
    }

    public function refunds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Refund::class);
    }

    public function getBudgetYear(): int
    {
        return 2022;
    }
}
