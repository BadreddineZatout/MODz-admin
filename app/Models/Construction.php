<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Construction extends Model
{
    protected $fillable = ['client_id', 'construction_type', 'description', 'date', 'hour', 'status', 'accepted_at', 'type', 'latitude', 'longitude'];

    public $timestamps = false;

    protected $casts = [
        'date' => 'date:Y-m-d',
        'accepted_at' => 'date:Y-m-d',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, '_category_construction', 'B', 'A');
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot(['quantity']);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, '_construction_employee', 'A', 'B');
    }

    public function assignedCategories(): array
    {
        if (! count($this->employees)) {
            return [];
        }
        if ($this->employees->first()->type == 'GROUP') {
            return $this->categories->pluck('id')->toArray();
        }
        $assignedCategories = [];
        $this->employees->each(function ($employee) use (&$assignedCategories) {
            $assignedCategories = array_merge($assignedCategories, array_intersect($this->categories->pluck('id')->toArray(), $employee->categories->pluck('id')->toArray()));
        });

        return $assignedCategories;
    }
}
