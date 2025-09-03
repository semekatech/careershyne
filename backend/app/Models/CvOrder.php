<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CvOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'fullname',
        'email',
         'status',
        'phone',
        'cv_path',
    ];
}
