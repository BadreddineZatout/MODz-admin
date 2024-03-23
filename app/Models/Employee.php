<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
        return $this->belongsToMany(Media::class, '_employeetomedia', 'A', 'B');
    }

    public function getID($order = 0): string
    {
        $ids = $this->media()->where('type', 'ID')->get();
        if (! $ids) {
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
}
