<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Subscription extends Model
{
    protected $fillable = ["user_id", "pack_id", "starts_at", "ends_at", "status"];

    public $timestamps = false;

    protected $casts = [
        "starts_at" => "date:Y-m-d",
        "ends_at" => "date:Y-m-d",
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pack(): BelongsTo
    {
        return $this->belongsTo(Pack::class);
    }
}
