<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function expenditures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expenditure::class)->where('batch_id', '>', 0);
    }

    public function controller(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function demand(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Demand::class);
    }

    public function track(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Track::class, 'trackable');
    }

    public function subBudgetHead()
    {
        return SubBudgetHead::where('code', $this->sub_budget_head_code)->first();
    }
}
