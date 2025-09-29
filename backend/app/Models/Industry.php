<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // One industry can have many users
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
