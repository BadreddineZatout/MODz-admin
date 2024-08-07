<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pack extends Model
{
    protected $fillable = ['name', 'price', 'duration', 'order_limit', 'description'];

    public $timestamps = false;

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
