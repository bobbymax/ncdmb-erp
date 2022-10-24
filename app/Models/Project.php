<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'service_category_id');
    }

    public function method(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProcurementMethod::class, 'procurement_method_id');
    }

    public function department(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id')->where('department_id', '>', 0);
    }

    public function milestones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Milestone::class);
    }

    public function timeline(): \Illuminate\Database\Eloquent\Relations\MorphOne
    {
        return $this->morphOne(Timeline::class, 'timelineable');
    }

    public function process(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ProcurementProcess::class);
    }

    public function bids(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Bid::class);
    }
}
