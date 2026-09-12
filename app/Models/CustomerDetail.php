<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerDetail extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
}
