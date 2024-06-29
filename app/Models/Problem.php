<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Problem extends Model
{
    //TODO: Add consturction
    protected $fillable = ['client_id', 'employee_id', 'report_date', 'order_id', 'description', 'reporter', 'is_treated'];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $casts = [
        'report_date' => 'date',
        'is_treated' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function clientScope(Builder $query): void
    {
        $query->where('reporter', 'CLIENT');
    }

    public function employeeScope(Builder $query): void
    {
        $query->where('reporter', 'EMPLOYEE');
    }
}
