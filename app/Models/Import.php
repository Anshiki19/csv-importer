<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CustomerDetail;

class Import extends Model
{
    protected $fillable = [
        'filename',
        'status',
        'total_rows',
        'imported_rows',
        'invalid_rows',
        'duplicate_rows',
        'failure_reason',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function errors(): HasMany
    {
        return $this->hasMany(ImportError::class);
    }

    public function customers()
    {
        return $this->hasMany(CustomerDetail::class);
    }
}