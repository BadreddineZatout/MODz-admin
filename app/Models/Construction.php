<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Construction extends Model
{
    protected $fillable = ['client_id', 'description', 'date', 'hour', 'status', 'accepted_at'];

    public $timestamps = false;

    protected $casts = [
        'date' => 'date:Y-m-d',
        'accepted_at' => 'date:Y-m-d',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, '_category_construction', 'B', 'A');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot(['quantity']);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(ConstructionOffer::class);
    }
}
