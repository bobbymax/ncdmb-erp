<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function staff(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(User::class);
    }

    public function demands(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Demand::class);
    }

    public function parent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'parentId');
    }

    public function directorate(): string
    {
        return $this->type === "directorate" ? $this->name : $this->parent->where('type', 'directorate')->name;
    }

    public function expenditures(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Expenditure::class);
    }

    public function requisitions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Requisition::class);
    }

    public function packages(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Distribution::class, 'distributionable');
    }

    public function responsibilities(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Responsibility::class);
    }

    public function tasks(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    public function advances(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TouringAdvance::class);
    }

    public function refunds(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Refund::class);
    }
}
