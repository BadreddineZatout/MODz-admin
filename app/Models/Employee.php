<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = ['first_name', 'last_name', 'phone', 'national_id', 'state_id', 'province_id', 'category_id', 'is_active', 'status'];

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

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Image::class, '_employeetoimage', 'A', 'B');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
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
}
