<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobType extends Model
{
    protected $fillable = ['name', 'has_items'];

    public $timestamps = false;

    protected $casts = [
        'has_items' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, '_category_jobtype', 'A', 'B');
    }
}
