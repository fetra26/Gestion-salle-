<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Direction extends Model
{
    protected $fillable = ['nom', 'description'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
