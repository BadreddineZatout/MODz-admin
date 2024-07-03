<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ConstructionOffer extends Model
{
    protected $fillable = ['employee_id', 'construction_id', 'can_travel', 'price', 'status'];

    protected $casts = [
        'can_travel' => 'boolean',
    ];

    public $timestamps = false;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function construction(): BelongsTo
    {
        return $this->belongsTo(Construction::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, '_category_construction_offer', 'B', 'A');
    }
}
