<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{ protected $guarded = [];
     protected $casts = [
        'platforms' => 'array',
        'assets' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'application_deadline' => 'date',
    ];
}
