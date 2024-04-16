<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = ['client_id', 'description', 'date', 'hour', 'category_id', 'job_type_id', 'status', 'is_urgent'];

    protected $casts = [
        'is_urgent' => 'boolean',
    ];

    public $timestamps = false;

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function jobType(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)->withPivot(['quantity']);
    }
}
