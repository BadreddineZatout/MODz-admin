<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SocialMedia extends Model
{
    protected $fillable = ['name'];

    public function providers(): BelongsToMany
    {
        return $this->belongsToMany(Provider::class, 'provider_socialmedia')->withPivot(['link']);
    }
}
