<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['name', 'profession', 'description', 'urgent', 'for_construction'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $casts = [
        'urgent' => 'boolean',
        'for_construction' => 'boolean',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, '_category_employee', 'A', 'B');
    }

    public function constructions(): BelongsToMany
    {
        return $this->belongsToMany(Construction::class, '_category_construction', 'A', 'B');
    }

    public function jobTypes(): BelongsToMany
    {
        return $this->belongsToMany(JobType::class, '_category_jobtype', 'A', 'B');
    }

    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
