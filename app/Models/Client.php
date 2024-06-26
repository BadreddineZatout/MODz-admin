<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    protected $fillable = ['first_name', 'last_name', 'phone', 'Status'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function profile(): HasOne
    {
        return $this->hasOne(ProfileUser::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function constructions(): HasMany
    {
        return $this->hasMany(Construction::class);
    }

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }
}
