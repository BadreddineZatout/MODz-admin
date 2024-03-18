<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Province extends Model
{
    protected $fillable = ['name', 'name_ar', 'city', 'city_ar', 'state_id'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }
}
