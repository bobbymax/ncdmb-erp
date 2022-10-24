<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $guarded = [''];

    public function roles()
    {
        return $this->morphToMany(Role::class, 'roleable');
    }
}
