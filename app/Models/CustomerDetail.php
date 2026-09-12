<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    protected $fillable = [
        'import_id',
        'name',
        'email',
        'phone',
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }
}
