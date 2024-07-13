<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = ['first_name', 'last_name', 'phone', 'national_id', 'state_id', 'province_id', 'is_active', 'status', 'can_work_construction', 'type'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $casts = [
        'is_active' => 'boolean',
        'can_work_construction' => 'boolean',
    ];

    public function profile(): HasOne
    {
        return $this->hasOne(ProfileUser::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, '_category_employee', 'B', 'A');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, '_employeetoimage', 'A', 'B');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function problems(): HasMany
    {
        return $this->hasMany(Problem::class);
    }

    public function getID($order = 0): string
    {
        $ids = $this->media()->where('type', 'ID')->get();
        if (! $ids->count()) {
            return '';
        }

        return env('API_URL').'/'.$ids[$order]->path;
    }

    public function getSelfie(): string
    {
        $selfie = $this->media()->where('type', 'SELFIE')->first();
        if (! $selfie) {
            return '';
        }

        return env('API_URL').'/'.$selfie->path;
    }

    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn () => "{$this->first_name} {$this->last_name}",
        );
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', 1);
    }

    public function scopeInactive(Builder $query): void
    {
        $query->where('is_active', 0);
    }
}
